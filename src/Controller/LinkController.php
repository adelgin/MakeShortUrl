<?php

namespace App\Controller;

use App\Entity\Link;
use App\Form\LinkTypeForm;
use App\Repository\LinkRepository;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

//var_dump($originalUrl);
//
//if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
//    return $this->render('link/index.html.twig', [
//        'form' => $form->createView(),
//        'short_url' => null,
//        'error' => 'Введён невалидный URL! Проверьте ссылку ещё раз!'
//    ]);
//}
//
//if ($expirationDate === false) {
//    return $this->render('link/index.html.twig', [
//        'short_url' => null,
//        'error' => 'Введена некорректная дата!'
//    ]);
//}


/**
 * Класс LinkController для сокращения ссылок
 */
final class LinkController extends AbstractController
{
    /**
     * Главный контроллер, обрабатывающий корневую ссылку '/'
     * @param Request $request принимает http запрос, но данный метод обрабатывает только POST
     * @return Response возвращает сокращенную ссылку и выводит её на страничку
     * @throws RandomException
     */
    #[Route('/', name: 'home')]
    public function index(Request $request, LinkRepository $repository): Response
    {
        date_default_timezone_set('Europe/Moscow');
        $shortURL = null;
        $link = new Link();
        $form = $this->createForm(LinkTypeForm::class, $link);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $characters = '0123456789abcdefghilkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

            do {
                $shortCode = '';
                for ($i = 0; $i < 6; $i++) {
                    $shortCode .= $characters[random_int(0, strlen($characters) - 1)];
                }
            } while (!is_null($repository->findByShortCode($shortCode)));

            is_null($link->getExpirationDate()) ?? $link->setExpirationDate(null);
            $link->setClickCount(0);
            $link->setCreationDate(new \DateTime());
            $link->setShortCode($shortCode);

            $repository->persist($link);
            $repository->flush();
            $shortURL = $request->getSchemeAndHttpHost() . '/short/' . $link->getShortCode();

            return $this->render('link/index.html.twig', [
                'form' => $form->createView(),
                'short_url' => $shortURL,
                'error' => NULL,
            ]);
        }

        return $this->render('link/index.html.twig', [
            'form' => $form->createView(),
            'short_url' => $shortURL,
            'error' => NULL,
        ]);

    }

    /**
     * Обработчик для short вида ссылок
     * @param string $code принимает сокращенный код, таким образом метод будет работать не только на localhost
     * @return Response редиректит на оригинальную страничку
     */
    #[Route('/short/{code}', name: 'short')]
    public function shortRedirect(string $code, LinkRepository $repository): Response
    {
        $link = $repository->findByShortCode($code);
        if (!is_null($link)) {
            $originalUrl = $link->getOriginalUrl();
        } else {
            return new Response($this->renderView('link/error.html.twig', ['error' => 'Ошибка! Ссылка никуда не ведёт!']), Response::HTTP_NOT_FOUND );
        }

        if (is_null($originalUrl) || !filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            return $this->render('link/error.html.twig', ['error' => 'Ошибка! Ссылка не найдена!']);
        }

        if ($link->getIsOneTime() === True) {
            $repository->remove($link);
        }
        else {
            $repository->clickUpdate($link);
            $repository->persist($link);
        }
        $repository->flush();

        return new RedirectResponse($originalUrl);
    }

    /**
     * Метод для вывода всех ссылок
     * @param Request $request запрос тут нужен для получения адреса откуда он отправлялся
     * @param LinkRepository $repository репозиторий получается через AutoWiring
     * @return Response
     */
    #[Route('/all', name: 'all')]
    public function all(Request $request, LinkRepository $repository): Response
    {
        $links = $repository->findAll();

        foreach ($links as $link) {
            if ($link->getExpirationDate() < new \DateTimeImmutable() && $link->getExpirationDate() !== null) {
                $repository->remove($link);
                $repository->flush();
            }
        }

        $links = $repository->findAll();

        return $this->render('link/all.html.twig', [
            'links' => $links,
            'short_url_start' => $request->getSchemeAndHttpHost() . '/short/',
        ]);
    }

    /**
     * Обработчик удаления ссылки
     * @param Request $request принимает POST-запрос. Это обработчик на форму.
     * @param LinkRepository $repository репозиторий получается через AutoWiring
     * @return Response перенаправляет на страницу со всеми ссылками
     */
    #[Route('/delete_links', name: 'delete')]
    public function delete(Request $request, LinkRepository $repository): Response
    {
        if ($request->isMethod('POST')) {
            $selected_links = $request->request->all('selected_links') ?? [];
            foreach ($selected_links as $linkId) {
                $repository->remove($repository->find($linkId));
            }
            $repository->flush();
        }

        return $this->redirectToRoute('all');
    }
}

<?php

namespace App\Controller;

use App\Repository\LinkRepository;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Класс LinkController
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
        $shortURL = null;
        $characters = '0123456789abcdefghilkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if ($request->isMethod('POST')) {
            $originalUrl = $request->request->get('url');

            if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
                return $this->render('link/index.html', [
                    'short_url' => null,
                    'error' => 'Введён невалидный URL! Проверьте ссылку ещё раз!'
                ]);
            }
            do {
                $shortCode = '';
                for ($i = 0; $i < 6; $i++) {
                    $shortCode .= $characters[random_int(0, strlen($characters) - 1)];
                }
            } while ($repository->findOneBy(['short_code' => $shortCode]));
            $link = $repository->addLink($originalUrl, $shortCode);
            $repository->persist($link);
            $repository->flush();
            $shortURL = $request->getSchemeAndHttpHost() . '/short/' . $link->getShortCode();
        }

        return $this->render('link/index.html', [
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
        if ($link) {
            $originalUrl = $link->getOriginalUrl();
        } else {
            return $this->render('link/error.html', ['error' => 'EROOR']);
        }


        if (!$originalUrl || !filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            throw $this->createNotFoundException('Error 404! Link not found!');
        }

        $repository->clickUpdate($link);
        $repository->persist($link);
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
        return $this->render('link/all.html', [
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

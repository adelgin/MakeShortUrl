<?php

namespace App\Repository;

use App\Entity\Link;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Класс для взаимодействия с базой данных links
 * @extends ServiceEntityRepository<Link>
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    /**
     * Метод для удаления объекта из базы данных
     * @param Link $link
     * @return void
     */
    public function remove(Link $link): void
    {
        $this->getEntityManager()->remove($link);
    }

    /**
     * Метод для применения изменений в базе данных
     * @return void
     */
    public function flush(): void {
        $this->getEntityManager()->flush();
    }

    /**
     * Возвращает объект по короткому идентификатору
     * @param string $shortCode
     * @return Link|null
     */
    public function findByShortCode(string $shortCode): ?Link
    {
        return $this->findOneBy(['short_code' => $shortCode]);
    }

    /**
     * Возвращает полную ссылку по короткому идентификатору
     * @param string $shortCode
     * @return string
     */
    public function findOriginalUrlByShortCode(string $shortCode): string
    {
        return $this->findOneBy(['short_code' => $shortCode])->getOriginalUrl();
    }

    /**
     * Метод для обновления базы данных после клика
     * @param Link $link
     * @return void
     */
    public function clickUpdate(Link $link): void {
        date_default_timezone_set('Europe/Moscow');
        $link->setClickCount($link->getClickCount() + 1);
        $link->setLastClickTimeDate(new \DateTime());
    }

    /**
     * Метод для insert или update
     * @param Link $link
     * @return void
     */
    public function persist(Link $link): void {
        $this->getEntityManager()->persist($link);
    }

    /**
     * Метод для уменьшения счётчика нажатия
     * @param Link $link
     * @return void
     */
    public function clickDelete(Link $link): void {
        $link->setClickCount($link->getClickCount() - 1 > 0 ? $link->getClickCount() - 1 : 0);
    }

    /**
     * Метод для получения всех значений (думал что нужно будет делать CRUD)
     * @return array
     */
    public function getAll(): array
    {
        return $this->findAll();
    }

    /**
     * Метод для добавления ссылки
     * @param string $originalUrl
     * @param string $shortCode
     * @return Link|null
     */
    public function addLink(string $originalUrl, string $shortCode): ?Link
    {
        date_default_timezone_set('Europe/Moscow');

        $link = new Link();
        $link->setOriginalUrl($originalUrl);
        $link->setClickCount(0);
        $link->setCreationDate(new \DateTime());
        $link->setShortCode($shortCode);
        return $link;
    }
}

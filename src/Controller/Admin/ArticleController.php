<?php

namespace App\Controller\Admin;

use App\ArticleGeneration\ArticleGenerator;
use App\ArticleGeneration\Strategy\FreeArticleGenerationStrategy;
use App\Factory\Article\ArticleFactory;
use App\Form\ArticleGenerationFormType;
use App\Form\Model\ArticleFormModel;
use App\Repository\ArticleRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use League\Flysystem\FilesystemException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Контроллер для статей в админском разделе
 *
 * @IsGranted("IS_EMAIL_CONFIRMED", message="Для доступа к этой странице подтвердите электронную почту")
 */
class ArticleController extends AbstractController
{
    /**
     * Отображает страницу истории сгенерированных статей
     *
     * ToDo: после привязки статей к пользователям отображать только статьи,
     *  сгенерированные конкретным пользователем
     *
     * @Route("/admin/article", name="app_admin_article" )
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(
        Request            $request,
        ArticleRepository  $articleRepository,
        PaginatorInterface $paginator
    ): Response
    {
        // ToDO: Создать связь между статьей и пользаком. Выводить для пользака только его статьи. Выводить админу
        //  статьи сгенерированные по умолчанию и его.
        $paginatedArticles = $paginator->paginate(
            $articleRepository->findAllArticlesQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/article/index.html.twig', [
            'articles' => $paginatedArticles,
        ]);
    }

    /**
     * Отображает страницу генерации статьи и обрабатывает форму генерации
     *
     * @Route("/admin/article/create", name="app_admin_article_create")
     * @param Request $request
     * @param ArticleGenerator $articleGenerator
     * @param FreeArticleGenerationStrategy $freeStrategy
     * @param EntityManagerInterface $em
     * @param ArticleRepository $articleRepository
     * @param FileUploader $fileUploader
     * @param ArticleFactory $articleFactory
     * @return Response
     * @throws FilesystemException
     * @throws Exception
     */
    public function create(
        Request                       $request,
        ArticleGenerator              $articleGenerator,
        FreeArticleGenerationStrategy $freeStrategy,
        EntityManagerInterface        $em,
        ArticleRepository             $articleRepository,
        FileUploader                  $fileUploader,
        ArticleFactory                $articleFactory
    ): Response
    {
        // TODo: Выполнить лимитирование генерации статьи на этапе реализации для API
        $form = $this->createForm(ArticleGenerationFormType::class);
        $form->handleRequest($request);
        $article = $request->get('articleId')
            ?
            $articleRepository->findOneBy([
                'id' => $request->get('articleId'),
            ])
            :
            false;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ArticleFormModel $articleModel */
            /**
             * 1) Выполнить вызов аплоадера для сохранения картинок, он вернет имена файлов,
             *   которые можно будет сохранить в БД и использовать далее для генерации
             * 2) Реализовать логику использования изображений для генерации статьи по ТЗ внутри стратегии
             */
            $articleModel = $form->getData();
            // Сохраняем изображения и записываем их имена в свойство ДТО
            $articleModel->images = $fileUploader->uploadManyFiles($articleModel->images);
            // Передаем ДТО в фабрику статей для формирования объекта статьи
            $article = $articleFactory->createFromModel($articleModel);
            $article->setBody(
                    $articleGenerator
                        ->setUser($this->getUser())
                        ->setArticleDTO($article)
                        ->setGenerationStrategy($freeStrategy)
                        ->generateArticle()
                );
            $em->persist($article);
            $em->flush();

            // Сообщение об успешном создании модуля
            $this->addFlash('success', 'Статья успешно сгенерирована');
            return $this->redirectToRoute('app_admin_article_create', [
                'articleId' => $article->getId(),
                ]);
        }

        return $this->render('admin/article/create.html.twig', [
            'articleForm' => $form->createView(),
            'errors' => $form->getErrors(true), // ошибки в форме
            'article' => $article,
        ]);
    }

    /**
     * Отображает страницу конкретной статьи
     *
     * ToDo: после привязки статей к пользователям отображать только статьи,
     *  сгенерированные конкретным пользователем
     *
     * @Route("/admin/article/{id}", name="app_admin_article_show")
     * @param int $id - идентификатор статьи
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function show(int $id, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBy(['id' => $id]);

        return $this->render('admin/article/show.html.twig', [
            'article' => $article
        ]);
    }
}

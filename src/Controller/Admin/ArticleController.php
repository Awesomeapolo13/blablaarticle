<?php

namespace App\Controller\Admin;

use App\ArticleGeneration\GenerationBlocker;
use App\Entity\Article;
use App\Factory\Article\ArticleFormModelFactory;
use App\Form\ArticleGenerationFormType;
use App\Handler\ArticleSaveHandler;
use App\Repository\ArticleRepository;
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
    ): Response {
        $paginatedArticles = $paginator->paginate(
            $articleRepository->findArticlesForUserQuery($this->getUser()),
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
     * @throws FilesystemException
     * @throws Exception
     */
    public function create(
        Request                $request,
        ArticleRepository      $articleRepository,
        GenerationBlocker      $blocker,
        ArticleSaveHandler      $saveHandler
    ): Response {
        /*
        TODo:
            1) Для удобства тестирования реализовать имперсонализацию
            2) Сделать копирование данных из сгенерированной статьи в новую форму
            3) Сделать adapter для генерации стаей посредством API
        */
        $user = $this->getUser();
        /** @var Article $article */
        $articleGenerated = $request->get('articleId')
            ?
            $articleRepository->findOneBy([
                'id' => $request->get('articleId'),
            ])
            :
            false;
        $form = $this->createForm(ArticleGenerationFormType::class);
        $form->handleRequest($request);
        // Проверяем необходима ли блокировка генерации статей, согласно уровню подписки пользователя
        $isBlocked = $blocker->isBlockBySubscription($user->getSubscription());
        $article = $saveHandler->handleAndSave($form, $user, $isBlocked);

        if ($article) {
            // Сообщение об успешном создании модуля
            $this->addFlash('success', 'Статья успешно сгенерирована');
            return $this->redirectToRoute('app_admin_article_create', [
                'articleId' => $article->getId(),
                ]);
        }

        return $this->render('admin/article/create.html.twig', [
            'articleForm' => $form->createView(),
            'errors' => $form->getErrors(true), // ошибки в форме
            'article' => $articleGenerated,
            'isGenerationBlocked' => false,
            'isBlocked' => $isBlocked,
        ]);
    }

    /**
     * Повторяет генерацию статью на основе уже сделанной
     *
     * @Route("/admin/article/{id}/repeat", name="app_admin_article_repeat")
     * @throws FilesystemException
     * @throws Exception
     */
    public function repeat(
        Article                 $article,
        Request                 $request,
        GenerationBlocker       $blocker,
        ArticleFormModelFactory $formModelFactory,
        ArticleSaveHandler      $saveHandler
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(
            ArticleGenerationFormType::class,
            $formModelFactory->createFromModel($article)
        );
        $form->handleRequest($request);
        $isBlocked = $blocker->isBlockBySubscription($user->getSubscription());

        $article = $saveHandler->handleAndSave($form, $user, $isBlocked);

        if ($article) {
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
            'isGenerationBlocked' => false,
            'isBlocked' => $isBlocked,
        ]);
    }

    /**
     * Отображает страницу конкретной статьи
     *
     * @Route("/admin/article/{id}", name="app_admin_article_show")
     * @param int $id - идентификатор статьи
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function show(int $id, ArticleRepository $articleRepository): Response
    {
        return $this->render('admin/article/show.html.twig', [
            'article' => $articleRepository->findOneBy([
                'id' => $id,
                'client' => $this->getUser()

            ])
        ]);
    }
}

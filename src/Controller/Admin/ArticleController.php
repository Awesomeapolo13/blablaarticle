<?php

namespace App\Controller\Admin;

use App\ArticleGeneration\GenerationBlocker;
use App\Entity\Article;
use App\Factory\Article\ArticleFormModelFactory;
use App\Form\ArticleGenerationFormType;
use App\Handler\ArticleSaveHandler;
use App\Repository\ArticleRepository;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use League\Flysystem\FilesystemException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        $isBlocked = $blocker->isBlockBySubscription($user);
        $article = $saveHandler->saveFromForm($form, $user, $isBlocked);

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
            'isMorphsAllowed' => $user
                    ->getSubscription()
                    ->getName() !== 'FREE',
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

        $article = $saveHandler->saveFromForm($form, $user, $isBlocked);

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

    /**
     * Создает статью по api
     *
     * @Route("/api/v1/admin/article/create/", name="app_admin_api_article_create")
     * @param Request $request
     * @param GenerationBlocker $blocker
     * @param ValidatorInterface $validator
     * @param ArticleFormModelFactory $formModelFactory
     * @param ArticleSaveHandler $saveHandler
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function apiCreate(
        Request                 $request,
        GenerationBlocker       $blocker,
        ValidatorInterface      $validator,
        ArticleFormModelFactory $formModelFactory,
        ArticleSaveHandler      $saveHandler
    ): JsonResponse {
        $response = [];
        $code = 400;
        $user = $this->getUser();
        $isBlocked = $blocker->isBlockBySubscription($user->getSubscription());
        $response['errors'][] = 'Превышен лимит создания статей, чтобы снять лимит улучшите подписку';
        if (!$isBlocked) {
            $model = $formModelFactory->createFromModel($request);
            $errors = $validator->validate($model);
            $response['errors'] = count($errors) > 0 ? $errors : null;
            $response = $response['errors'] ?? $saveHandler->saveArticle($model, $user);
            $code = 200;
        }

        return $this->json(
            $response,
            $code,
            [],
            [
                'groups' => [
                    'api',
                ]
            ]
        );
    }
}

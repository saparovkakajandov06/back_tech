<?php

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use Illuminate\Http\Request;
use App\Responses\ApiError;
use App\Responses\ApiSuccess;
use App\Article;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Role\RoleChecker;
use App\Role\UserRole;

class ArticleController extends Controller
{
     /**
     * @OA\Post(
     *      path="/api/articles",
     *      operationId="store",
     *      summary="Creating an article",
     *      tags={"Article"},
     *      description="Создание статьи",
     *      @OA\Parameter(
     *          name="slug",
     *          in="query",
     *          description="The slug parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="string"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="cover",
     *          in="query",
     *          description="The cover parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="string"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="heading",
     *          in="query",
     *          description="The heading parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="string"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="description",
     *          in="query",
     *          description="The description parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="string"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="tags",
     *          in="query",
     *          description="The tags parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="string"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="article",
     *          in="query",
     *          description="The article parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="string"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="headtitle",
     *          in="query",
     *          description="The headtitle parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="string"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="headdescription",
     *          in="query",
     *          description="The headdescription parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="string"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Article created"
     *      ),
     *     )
     * Articles list
     */
    #[Endpoint('articles')]
    #[Verbs(D::POST)]
    #[Role('ROLE_SEO')]
    #[Group('seo')]
    #[Text('Создание статьи')]
    #[Param('slug', true, D::STRING)]
    #[Param('cover', true, D::STRING)]
    #[Param('heading', true, D::STRING)]
    #[Param('description', true, D::STRING)]
    #[Param('tags', true, D::STRING)]
    #[Param('article', true, D::STRING)]
    #[Param('headtitle', true, D::STRING)]
    #[Param('headdescription', true, D::STRING)]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string',
            'cover' => 'required|string',
            'heading' => 'required|string',
            'description' => 'required|string',
            'tags' => 'required',
            'article' => 'required|string',
            'headtitle' => 'required|string',
            'headdescription' => 'required|string',
        ]);

        if ($validator->fails()) {
            return new ApiError('Validation error', $validator->errors());
        }

        $article = Auth::user()->articles()->create($request->all());

        return new ApiSuccess('Article created', $article);
    }

     /**
     * @OA\Get(
     *      path="/api/articles",
     *      operationId="index",
     *      summary="Articles list",
     *      tags={"Article"},
     *      description="Получение списка статей",
     *      @OA\Parameter(
     *          name="offset",
     *          in="query",
     *          description="The offset parameter in query",
     *          required=false,
     *          @OA\Schema(
     *             type="number"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="The limit parameter in query",
     *          required=false,
     *          @OA\Schema(
     *             type="number"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="sort",
     *          in="query",
     *          description="The sort parameter in query",
     *          required=false,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success"
     *      ),
     *     )
     * Articles list
     */
    #[Endpoint('articles')]
    #[Verbs(D::GET)]
    #[Group('seo')]
    #[Text('Получение списка статей')]
    #[Param('offset', false, D::INT)]
    #[Param('limit', false, D::INT)]
    #[Param('sort', false, D::OTHER, 'Сортировка по параметру', 'id')]
    public function index(Request $request)
    {
        $offset = (int) $request->get('offset', 0);
        $limit = (int) $request->get('limit', 9);
        $sort = $request->get('sort', 'created_at');

        $articles = Article::offset($offset)
                      ->limit($limit)
                      ->orderBy($sort, 'DESC')
                      ->get();
        $result = [
            'items' => $articles,
            'meta' => [
                'total' => Article::count(),
            ]
        ];

        return new ApiSuccess('Articles list', $result);
    }

     /**
     * @OA\Get(
     *      path="/api/articles/{slug}",
     *      operationId="show",
     *      summary="Getting a unique article",
     *      tags={"Article"},
     *      description="Получение уникальной статьи",
     *      @OA\Parameter(
     *          name="slug",
     *          in="query",
     *          description="The offset parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="string"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success"
     *      ),
     *     )
     * Articles list
     */
    #[Endpoint('articles/{slug}')]
    #[Verbs(D::GET)]
    #[Group('seo')]
    #[Text('Получение уникальной статьи по slug')]
    #[Param('slug', true, D::STRING)]
    public function show($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        $article->views += 1;
        
        $article->save();

        return new ApiSuccess('Article', $article);
    }

     /**
     * @OA\Get(
     *      path="/api/articles_by_user",
     *      operationId="articlesByUser",
     *      summary="Retrieving user articles",
     *      tags={"Article"},
     *      description="Получение статей пользователя",
     *     @OA\SecurityScheme(
     *          securityScheme="bearerAuth",
     *          in="header",
     *          name="bearerAuth",
     *          scheme="bearer",
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          in="query",
     *          description="The offset parameter in query",
     *          required=false,
     *          @OA\Schema(
     *             type="number"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="The limit parameter in query",
     *          required=false,
     *          @OA\Schema(
     *             type="number"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Articles list by user"
     *      ),
     *     )
     * Retrieving user articles
     */
    #[Endpoint('articles_by_user')]
    #[Verbs(D::GET)]
    #[Role('ROLE_SEO')]
    #[Group('seo')]
    #[Text('Получение статей пользователя')]
    #[Param('limit', false, D::INT)]
    #[Param('offset', false, D::INT)]
    public function articlesByUser(Request $request)
    {        
        $articles = Article::orderBy('created_at', 'DESC')
            ->offset($request->get('offset', 0))
            ->limit($request->get('limit', 10))
            ->get();
  
        $data = [
            'items' => $articles,
            'meta' => [
                'offset'  => $request->get('offset', 0),
                'limit'   => $request->get('limit', 10),
                'total'   => Article::count(),
            ]
        ];
           
        return new ApiSuccess('Articles list by user', $data);
    }

     /**
     * @OA\Delete(
     *      path="/api/articles/{id}",
     *      operationId="destroy",
     *      summary="destroy articles",
     *      tags={"Article"},
     *      description="Удаление статей",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="The request id parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="number"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Articles deleted successfully"
     *      ),
     *     )
     * destroy articles
     */
    #[Endpoint('articles/{id}')]
    #[Verbs(D::DELETE)]
    #[Role('ROLE_SEO')]
    #[Group('seo')]
    #[Text('Удаление статьи')]
    #[Param('id', true, D::INT)]
    public function destroy($id)
    {        
        $article = Article::findOrFail($id);

        $article->delete();

        return new ApiSuccess('Article deleted successfully.', $article);
    }

     /**
     * @OA\Put(
     *      path="/api/articles/{id}",
     *      operationId="update",
     *      summary="update articles",
     *      tags={"Article"},
     *      description="Обновление статей",
     *      @OA\Parameter(
     *          name="request",
     *          in="query",
     *          description="The request object parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="object"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="ok"
     *      ),
     *     )
     * Rupdate articles
     */
    #[Endpoint('articles/{id}')]
    #[Verbs(D::POST)]
    #[Role('ROLE_SEO')]
    #[Group('seo')]
    #[Text('Редактирование статьи')]
    #[Param('id', true, D::INT)]
    #[Param('slug', false, D::STRING)]
    #[Param('cover', false, D::STRING)]
    #[Param('heading', false, D::STRING)]
    #[Param('description', false, D::STRING)]
    #[Param('tags', false, D::STRING)]
    #[Param('article', false, D::STRING)]
    #[Param('headtitle', false, D::STRING)]
    #[Param('headdescription', false, D::STRING)]
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        
        $article->update($request->all());

        return new ApiSuccess('ok', $article);
    }
}

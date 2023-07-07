<?php
namespace App\Services;

use App\Repositories\BookRepository;
use App\Repositories\BookImageRepository;
use App\Resources\BookResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\CustomException;

class BookService extends Service
{
    protected $bookRepository;
    protected $bookImageRepository;

    public function __construct(
        BookRepository $bookRepository,
        BookImageRepository $bookImageRepository,
    ) {
        $this->bookRepository           = $bookRepository;
        $this->bookImageRepository      = $bookImageRepository;
    }

    public function getList($searchParams)
    {
        $lists           = [];
        $getAuthUser     = Auth::user();
        $limit           = $searchParams['size'] ?? config('code.itemPerPage');
        $getLists        = $this->bookRepository->pageList(
            $limit,
            $getAuthUser->id,
        );
        if (!$getLists) {
            return [
                'data'          => [],
                'total'         => 0,
                'per_page'      => 0,
                'current_page'  => 0
            ];
        }

        $lists     = BookResource::collection($getLists);
        return [
            'data'    => [
                $lists,
            ],
            'total'         => $getLists->total(),
            'per_page'      => $getLists->perPage(),
            'current_page'  => $getLists->currentPage()
        ];
    }

    public function getOne($id)
    {
        $getAuthUser     = Auth::user();
        $getOne          = $this->bookRepository->one(
            $id,
            $getAuthUser->id,
        );
        if (!$getOne) {
            throw new CustomException('The book does not exist');
        }

        return new BookResource($getOne);
    }

    public function create($params)
    {
        $code           = config('code.apiSuccess');
        $message        = '';
        $httpStatusCode = 201;
        $getAuthUser    = Auth::user();
        DB::connection()->beginTransaction();
        try {
            $createArray = [
                'member_id'                    => $getAuthUser->id,
                'title'                        => $params['title'],
                'author'                       => $params['author'],
                'publication_date'             => $params['publicationDate'],
                'category'                     => $params['category'],
                'price'                        => $params['price'],
                'quantity'                     => $params['quantity'],
            ];

            $book = $this->bookRepository->create(
                'book',
                $createArray
            );

            foreach ($params['images'] as $image) {
                $this->bookImageRepository->create(
                    'bookimage',
                    [
                        'book_id'    => $book['model']->id,
                        'name'       => $image['name'],
                        'path'       => $image['path'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                );
            }

            DB::table('book_edit_logs')->insert(
                [
                    'book_id'                       => $book['model']->id,
                    'member_id'                     => $getAuthUser->id,
                    'title'                         => $params['title'],
                    'author'                        => $params['author'],
                    'publication_date'              => $params['publicationDate'],
                    'category'                      => $params['category'],
                    'price'                         => $params['price'],
                    'quantity'                      => $params['quantity'],
                    'images'                        => json_encode($params['images']),
                    'created_at'                    => date('Y-m-d H:i:s')
                ]
            );
            DB::connection()->commit();
        } catch (\Exception $e) {
            DB::connection()->rollback();
            Log::error('Create Book Error:' . $e->getMessage());
            $message        = '新增 Book 失敗.';
            $code           = config('code.apiExceptionError');
            $httpStatusCode = 400;
        }

        return [
            'code'              => $code,
            'message'           => $message,
            'httpStatusCode'    => $httpStatusCode
        ];
    }

    public function update($params, $id)
    {
        $code           = config('code.apiSuccess');
        $message        = '';
        $httpStatusCode = 201;
        $getAuthUser    = Auth::user();
        $getBookOne     = $this->bookRepository->findBy('id', $id);
        if (!$getBookOne) {
            $httpStatusCode = 404;
            $code           = config('code.apiExceptionError');
            $message        = 'The book does not exist';
            return [
                'code'              => $code,
                'message'           => $message,
                'httpStatusCode'    => $httpStatusCode
            ];
        }

        if ($getBookOne->member_id !== $getAuthUser->id) {
            $httpStatusCode = 400;
            $code           = config('code.apiExceptionError');
            $message        = 'permission denied';
            return [
                'code'              => $code,
                'message'           => $message,
                'httpStatusCode'    => $httpStatusCode
            ];
        }

        DB::connection()->beginTransaction();
        try {
            $getBookOne->title              = $params['title'];
            $getBookOne->author             = $params['author'];
            $getBookOne->publication_date   = $params['publicationDate'];
            $getBookOne->category           = $params['category'];
            $getBookOne->price              = $params['price'];
            $getBookOne->quantity           = $params['quantity'];
            $getBookOne->save();
            $this->bookImageRepository->deleterByBookId($getBookOne->id);
            foreach ($params['images'] as $image) {
                $this->bookImageRepository->create(
                    'bookimage',
                    [
                        'book_id'    => $getBookOne->id,
                        'name'       => $image['name'],
                        'path'       => $image['path'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                );
            }

            DB::table('book_edit_logs')->insert(
                [
                    'book_id'                       => $getBookOne->id,
                    'member_id'                     => $getAuthUser->id,
                    'title'                         => $params['title'],
                    'author'                        => $params['author'],
                    'publication_date'              => $params['publicationDate'],
                    'category'                      => $params['category'],
                    'price'                         => $params['price'],
                    'quantity'                      => $params['quantity'],
                    'images'                        => json_encode($params['images']),
                    'created_at'                    => date('Y-m-d H:i:s')
                ]
            );
            DB::connection()->commit();
        } catch (\Exception $e) {
            DB::connection()->rollback();
            Log::error('Update Book Error:' . $e->getMessage());
            $message        = '修改 Book 失敗.';
            $code           = config('code.apiExceptionError');
            $httpStatusCode = 400;
        }

        return [
            'code'              => $code,
            'message'           => $message,
            'httpStatusCode'    => $httpStatusCode
        ];
    }

    public function delete($id)
    {
        $code           = config('code.apiSuccess');
        $message        = '';
        $httpStatusCode = 204;
        $getAuthUser    = Auth::user();
        $getBookOne     = $this->bookRepository->findBy('id', $id);
        if (!$getBookOne) {
            $httpStatusCode = 404;
            $code           = config('code.apiExceptionError');
            $message        = 'The book does not exist';
            return [
                'code'              => $code,
                'message'           => $message,
                'httpStatusCode'    => $httpStatusCode
            ];
        }

        if ($getBookOne->member_id !== $getAuthUser->id) {
            $httpStatusCode = 400;
            $code           = config('code.apiExceptionError');
            $message        = 'permission denied';
            return [
                'code'              => $code,
                'message'           => $message,
                'httpStatusCode'    => $httpStatusCode
            ];
        }

        DB::connection()->beginTransaction();
        try {
            $this->bookRepository->delete($id);
            $this->bookImageRepository->deleterByBookId($id);
            DB::connection()->commit();
        } catch (\Exception $e) {
            DB::connection()->rollback();
            Log::error('Delete Book Error:' . $e->getMessage());
            $message        = '刪除 Book 失敗.';
            $code           = config('code.apiExceptionError');
            $httpStatusCode = 400;
        }

        return [
            'code'              => $code,
            'message'           => $message,
            'httpStatusCode'    => $httpStatusCode
        ];
    }
}

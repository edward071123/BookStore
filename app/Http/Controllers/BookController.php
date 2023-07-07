<?php
namespace App\Http\Controllers;

use App\Help\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\BookService;
use App\Requests\BookRequest;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * 書籍列表.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $searchParams = $request->all();
        $response     = $this->bookService->getList($searchParams);
        return response()->json($response);
    }

    /**
     * 書籍單一.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function one($id)
    {
        $response     = $this->bookService->getOne($id);
        return response()->json($response);
    }

    /**
     * 新增書籍.
     *
     * @param  BookRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(BookRequest $request)
    {
        $request->validated();
        $response = $this->bookService->create($request->all());
        return response()->json(
            new JsonResponse(false, [], $response['code'], $response['message']),
            $response['httpStatusCode']
        );
    }

    /**
     * 修改書籍.
     *
     * @param  BookRequest $request
     * @return \Illuminate\Http\Response
     */
    public function edit(BookRequest $request, $id)
    {
        $request->validated();
        $response = $this->bookService->update($request->all(), $id);
        return response()->json(
            new JsonResponse(false, [], $response['code'], $response['message']),
            $response['httpStatusCode']
        );
    }

    /**
     * 刪除書籍.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $response = $this->bookService->delete($id);
        return response()->json(
            new JsonResponse(false, [], $response['code'], $response['message']),
            $response['httpStatusCode']
        );
    }
}

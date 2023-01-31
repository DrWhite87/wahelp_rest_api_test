<?php

namespace App\Controllers;

use App\Models\RandomNumModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RandomNumController extends BaseController
{
    /**
     * Генерация случайного числа
     * POST "/generate"
     *
     * @param Response $response
     * @throws \Exception
     */
    public function generate(Response $response): Response
    {
        $model = new RandomNumModel();

        return $response->setContent(json_encode([
            'status' => Response::HTTP_OK,
            'data' => $model->create()
        ]));
    }

    /**
     * Получение числа по ID
     * GET "/retrieve/:id"
     *
     * @param Request $request
     * @param Response $response
     * @param $id
     * @return Response
     */
    public function retrieve(Request $request, Response $response, $id): Response
    {
        $model = (new RandomNumModel())->getOne($id);

        if(empty($model)){
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response->setContent(json_encode([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Element not found.'
            ]));
        }

        return $response->setContent(json_encode([
            'status' => Response::HTTP_OK,
            'data' => $model
        ]));
    }

    /**
     * Получение списка элементов
     * GET "/list?limit=10&offset=0"
     *
     * @param Request $request
     * @param Response $response
     * @param $id
     * @return Response
     */
    public function list(Request $request, Response $response): Response
    {

        $model = new RandomNumModel();
        return $response->setContent(json_encode([
            'status' => Response::HTTP_OK,
            'data' => $model->getAll([
                'limit' => $request->query->has('limit') ? $request->get('limit') : null,
                'offset' => $request->query->has('offset') ? $request->get('offset') : 0
            ])
        ]));
    }
}
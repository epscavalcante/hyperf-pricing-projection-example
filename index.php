<?php

require_once __DIR__ . '/vendor/autoload.php';

use Hyperf\Nano\Factory\AppFactory;
use Src\Application\UseCases\CreateLayer\CreateLayer;
use Src\Application\UseCases\CreateLayer\CreateLayerInput;
use Src\Application\UseCases\ListLayers\ListLayers;
use Src\Application\UseCases\ListLayers\ListLayersOutputItem;
use Src\Domain\Exceptions\AlreadyExistsException;
use Src\Domain\Repositories\LayerRepository;
use Src\Infrastructure\Repositories\LayerQueryBuilderRepository;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Src\Application\UseCases\CreatePrice\CreatePrice;
use Src\Application\UseCases\CreatePrice\CreatePriceInput;
use Src\Application\UseCases\CreateProduct\CreateProduct;
use Src\Application\UseCases\CreateProduct\CreateProductInput;
use Src\Application\UseCases\ListPrices\ListPrices;
use Src\Application\UseCases\ListPrices\ListPricesInput;
use Src\Application\UseCases\ListPrices\ListPricesOutputItem;
use Src\Application\UseCases\ListProducts\ListProducts;
use Src\Application\UseCases\ListProducts\ListProductsOutputItem;
use Src\Domain\Exceptions\NotFoundException;
use Src\Domain\Repositories\PriceRepository;
use Src\Domain\Repositories\ProductRepository;
use Src\Infrastructure\Repositories\PriceQueryBuilderRepository;
use Src\Infrastructure\Repositories\ProductQueryBuilderRepository;
use function Hyperf\Support\env;

$app = AppFactory::create(
    host: '0.0.0.0',
    port: 9501,
    dependencies: [
        ProductRepository::class => ProductQueryBuilderRepository::class,
        LayerRepository::class => LayerQueryBuilderRepository::class,
        PriceRepository::class => PriceQueryBuilderRepository::class,
    ]
);

$app->addExceptionHandler(function (Throwable $throwable, ResponseInterface $response) {
    if ($throwable instanceof NotFoundException) {
        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                new SwooleStream(
                    json_encode(
                        value: [
                            'message' => $throwable->getMessage()
                        ],
                        flags: JSON_UNESCAPED_UNICODE
                    )
                )
            );
    }

    if ($throwable instanceof AlreadyExistsException) {
        return $response
            ->withStatus(409)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                new SwooleStream(
                    json_encode(
                        value: [
                            'message' => $throwable->getMessage()
                        ],
                        flags: JSON_UNESCAPED_UNICODE
                    )
                )
            );
    }


    dd($throwable, $response);
    //dump('erro', $throwable);
    //return $response->withStatus('418')->withBody(new SwooleStream('Deu erro'));
});

$app->get('/', function () {
    return [
        'data' => "hello World",
    ];
});

$app->addGroup(
    prefix: '/products',
    callback: function () use ($app) {
        $app->get('', function () {
            $useCase = new ListProducts(
                productRepository: $this->get(ProductRepository::class),
            );
            $output = $useCase->execute();
            return [
                'total' => $output->total,
                'items' => array_map(
                    callback: function (ListProductsOutputItem $item) {
                        return [
                            'id' =>  $item->id,
                            'name' =>  $item->name,
                        ];
                    },
                    array: $output->items
                )
            ];
        });

        $app->post('', function () {
            $useCase = new CreateProduct(
                productRepository: $this->get(ProductRepository::class),
            );
            $input = new CreateProductInput(
                name: $this->request->input('name'),
            );
            $output = $useCase->execute($input);
            return $this->response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(
                    new SwooleStream(
                        json_encode(
                            value: [
                                'product_id' => $output->productId
                            ],
                            flags: JSON_UNESCAPED_UNICODE
                        )
                    )
                );
        });
    }
);

$app->addGroup(
    prefix: '/layers',
    callback: function () use ($app) {
        $app->get('', function () {
            $useCase = new ListLayers(
                layerRepository: $this->get(LayerRepository::class),
            );
            $output = $useCase->execute();
            return [
                'total' => $output->total,
                'items' => array_map(
                    callback: function (ListLayersOutputItem $item) {
                        return [
                            'id' =>  $item->id,
                            'code' =>  $item->code,
                            'type' =>  $item->type,
                            'description' =>  $item->description,
                        ];
                    },
                    array: $output->items
                )
            ];
        });

        $app->post('', function () {
            $input = new CreateLayerInput(
                code: $this->request->input('code'),
                type: $this->request->input('type'),
                parentId: $this->request->input('parent_id', null),
                value: $this->request->input('value', 0),
                description: $this->request->input('description'),
            );
            $useCase = new CreateLayer(
                layerRepository: $this->get(LayerRepository::class),
            );
            $output = $useCase->execute($input);

            return $this->response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(
                    new SwooleStream(
                        json_encode(
                            value: [
                                'layer_id' => $output->layerId
                            ],
                            flags: JSON_UNESCAPED_UNICODE
                        )
                    )
                );
        });
    }
);

$app->addGroup(
    prefix: '/prices',
    callback: function () use ($app) {
        $app->get('', function () {
            $useCase = new ListPrices(
                priceRepository: $this->get(PriceRepository::class),
            );
            $input = new ListPricesInput(
                layerIds: $this->request->input('layers_id', []),
                productIds: $this->request->input('products_id', []),
            );
            $output = $useCase->execute($input);
            return [
                'total' => $output->total,
                'items' => array_map(
                    callback: function (ListPricesOutputItem $item) {
                        return [
                            'id' =>  $item->id,
                            'product_id' =>  $item->productId,
                            'layer_id' =>  $item->layerId,
                            'value' =>  $item->value,
                        ];
                    },
                    array: $output->items
                )
            ];
        });

        $app->post('', function () {
            $input = new CreatePriceInput(
                layerId: $this->request->input('layer_id'),
                productId: $this->request->input('product_id'),
                value: $this->request->input('value'),
            );
            $useCase = new CreatePrice(
                layerRepository: $this->get(LayerRepository::class),
                priceRepository: $this->get(PriceRepository::class),
                productRepository: $this->get(ProductRepository::class),
            );
            $output = $useCase->execute($input);

            return $this->response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(
                    new SwooleStream(
                        json_encode(
                            value: [
                                'price_id' => $output->priceId
                            ],
                            flags: JSON_UNESCAPED_UNICODE
                        )
                    )
                );
        });
    }
);

$app->config([
    'databases' => [
        'default' => [
            'driver' => env('DB_CONNECTION', 'mysql'),
            'host' => env('DB_HOST', 'mysql'),
            'port' => env('DB_PORT', 3306),
            'database' => env('DB_DATABASE', 'db'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', 'root'),
        ]
    ]
]);

$app->run();

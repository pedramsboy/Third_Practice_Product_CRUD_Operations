<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\ProductRepository;
use Valitron\Validator;
use App\Repositories\FileService;

class Products
{
    public function __construct(private ProductRepository $repository,
                                private Validator $validator,
                                private FileService $fileService,)
    {
        $this->validator->mapFieldsRules([
            'name' => ['required'],
            'size' => ['required', 'integer', ['min', 1]]
        ]);
    }

    public function show(Request $request, Response $response, string $id): Response
    {
        $product = $request->getAttribute('product');

        $body = json_encode($product);

        $response->getBody()->write($body);

        return $response;
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        //This Line Is Added
        $files = $request->getUploadedFiles();

        $this->validator = $this->validator->withData($body);

        if ( ! $this->validator->validate()) {

            $response->getBody()
                ->write(json_encode($this->validator->errors()));

            return $response->withStatus(422);

        }

        // Handle file upload if present
        $fileData = [];
        if (!empty($files['file'])) {
            try {
                $fileData = $this->fileService->handleUpload([
                    'name' => $files['file']->getClientFilename(),
                    'type' => $files['file']->getClientMediaType(),
                    'size' => $files['file']->getSize(),
                    'tmp_name' => $files['file']->getStream()->getMetadata('uri'),
                    'error' => $files['file']->getError()
                ]);
            } catch (\RuntimeException $e) {
                $response->getBody()->write(json_encode(['file' => $e->getMessage()]));
                return $response->withStatus(422);
            }
        }

        // Merge file data with product data
        $productData = array_merge($body, $fileData);

        //$id = $this->repository->create($body);

        $id = $this->repository->create($productData);

        $body = json_encode([
            'message' => 'Product created',
            'id' => $id
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(201);
    }

    public function update(Request $request, Response $response, string $id): Response
    {
        $body = $request->getParsedBody();

        //These Lines Are Added
        $files = $request->getUploadedFiles();
        $existingProduct = $request->getAttribute('product');

        $this->validator = $this->validator->withData($body);

        if ( ! $this->validator->validate()) {

            $response->getBody()
                ->write(json_encode($this->validator->errors()));

            return $response->withStatus(422);

        }

        $fileData = [];

        // Handle new file upload if present
        if (!empty($files['file'])) {
            try {
                // Delete old file if exists
                if (!empty($existingProduct['file_path'])) {
                    $this->fileService->deleteFile($existingProduct['file_path']);
                }

                $fileData = $this->fileService->handleUpload([
                    'name' => $files['file']->getClientFilename(),
                    'type' => $files['file']->getClientMediaType(),
                    'size' => $files['file']->getSize(),
                    'tmp_name' => $files['file']->getStream()->getMetadata('uri'),
                    'error' => $files['file']->getError()
                ]);
            } catch (\RuntimeException $e) {
                $response->getBody()->write(json_encode(['file' => $e->getMessage()]));
                return $response->withStatus(422);
            }
        }

        // Merge file data with product data
        $productData = array_merge($body, $fileData);

        //$rows = $this->repository->update((int) $id, $body);

        $rows = $this->repository->update((int) $id, $productData);

        $body = json_encode([
            'message' => 'Product updated',
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }

    public function delete(Request $request, Response $response, string $id): Response
    {
        $rows = $this->repository->delete($id);

        $body = json_encode([
            'message' => 'Product deleted',
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;


    }

    public function getFile(Request $request, Response $response, string $id): Response
    {
        $product = $request->getAttribute('product');

        if (empty($product['file_path'])) {
            return $response->withStatus(404);
        }

        $filePath = $this->fileService->getFile($product['file_path']);

        if (!$filePath) {
            return $response->withStatus(404);
        }

        $fileContents = file_get_contents($filePath);
        $response->getBody()->write($fileContents);

        return $response->withHeader('Content-Type', $product['file_type'])
            ->withHeader('Content-Disposition', 'inline; filename="' . $product['file_name'] . '"');
    }

    public function updateFile(Request $request, Response $response, string $id): Response
    {
        $files = $request->getUploadedFiles();
        $existingProduct = $request->getAttribute('product');

        if (empty($files['file'])) {
            $response->getBody()->write(json_encode(['error' => 'No file provided']));
            return $response->withStatus(422);
        }

        try {
            // Delete old file if exists
            if (!empty($existingProduct['file_path'])) {
                $this->fileService->deleteFile($existingProduct['file_path']);
            }

            $fileData = $this->fileService->handleUpload([
                'name' => $files['file']->getClientFilename(),
                'type' => $files['file']->getClientMediaType(),
                'size' => $files['file']->getSize(),
                'tmp_name' => $files['file']->getStream()->getMetadata('uri'),
                'error' => $files['file']->getError()
            ]);

            // Update only file fields in database
            $this->repository->updateFile((int)$id, $fileData);

            $response->getBody()->write(json_encode([
                'message' => 'File updated successfully'
            ]));

            return $response;

        } catch (\RuntimeException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(422);
        }
    }
}
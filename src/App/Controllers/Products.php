<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\ProductRepository;
use Valitron\Validator;
use App\Repositories\FileService;
use App\Repositories\LoggerService;

class Products
{
    public function __construct(private ProductRepository $repository,
                                private Validator $validator,
                                private FileService $fileService,
                                private LoggerService $logger)
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

        //added Line(logger)
        $this->logger->debug('Product details viewed', ['product_id' => $id], 'product');

        return $response;
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        //This Line Is Added(File Handling)
        $files = $request->getUploadedFiles();

        $this->validator = $this->validator->withData($body);

        if ( ! $this->validator->validate()) {

            $errors = $this->validator->errors();

            //This Line Is Added(logger)
            $this->logger->warning('Product creation validation failed', [
                'errors' => $errors,
                'input_data' => $body
            ], 'product');

            $response->getBody()
                ->write(json_encode($errors));

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

                $this->logger->info('File uploaded for product creation', [
                    'file_name' => $fileData['file_name'] ?? null
                ], 'product');

            }

            catch (\RuntimeException $e) {

                $this->logger->error('File upload failed for product creation', [
                    'error' => $e->getMessage(),
                    'file_name' => $files['file']->getClientFilename()
                ], 'product');

                $response->getBody()->write(json_encode(['file' => $e->getMessage()]));
                return $response->withStatus(422);
            }
        }

        try {
            // Merge file data with product data
            $productData = array_merge($body, $fileData);

            //$id = $this->repository->create($body);

            $id = $this->repository->create($productData);

//        $body = json_encode([
//            'message' => 'Product created',
//            'id' => $id
//        ]);

            $responseData = [
                'message' => 'Product created',
                'id' => $id
            ];

            $body = json_encode($responseData);

            $response->getBody()->write($body);

            $this->logger->info('Product created via API', [
                'product_id' => $id,
                'response' => $responseData
            ], 'product');

            return $response->withStatus(201);
        }

        catch (\Exception $e) {
            $this->logger->error('Product creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 'product');

            $response->getBody()->write(json_encode(['error' => 'Internal server error']));
            return $response->withStatus(500);
        }
    }

    public function update(Request $request, Response $response, string $id): Response
    {
        $body = $request->getParsedBody();

        //These Lines Are Added
        $files = $request->getUploadedFiles();
        $existingProduct = $request->getAttribute('product');

        $this->validator = $this->validator->withData($body);

        if ( ! $this->validator->validate()) {

            $errors = $this->validator->errors();

            $this->logger->warning('Product update validation failed', [
                'product_id' => $id,
                'errors' => $errors,
                'input_data' => $body
            ], 'product');

            $response->getBody()
                ->write(json_encode($errors));

            return $response->withStatus(422);

        }

        $fileData = [];

        // Handle new file upload if present
        if (!empty($files['file'])) {
            try {
                // Delete old file if exists
                if (!empty($existingProduct['file_path'])) {
                    $this->fileService->deleteFile($existingProduct['file_path']);

                    $this->logger->info('Old product file deleted', [
                        'product_id' => $id,
                        'old_file_path' => $existingProduct['file_path']
                    ], 'product');
                }

                $fileData = $this->fileService->handleUpload([
                    'name' => $files['file']->getClientFilename(),
                    'type' => $files['file']->getClientMediaType(),
                    'size' => $files['file']->getSize(),
                    'tmp_name' => $files['file']->getStream()->getMetadata('uri'),
                    'error' => $files['file']->getError()
                ]);

                $this->logger->info('New file uploaded for product update', [
                    'product_id' => $id,
                    'file_name' => $fileData['file_name'] ?? null
                ], 'product');

            } catch (\RuntimeException $e) {
                $this->logger->error('File upload failed for product update', [
                    'product_id' => $id,
                    'error' => $e->getMessage()
                ], 'product');
                $response->getBody()->write(json_encode(['file' => $e->getMessage()]));
                return $response->withStatus(422);
            }
        }

        try {
            // Merge file data with product data
            $productData = array_merge($body, $fileData);

            //$rows = $this->repository->update((int) $id, $body);

            $rows = $this->repository->update((int)$id, $productData);

//            $body = json_encode([
//                'message' => 'Product updated',
//                'rows' => $rows
//            ]);
            $responseData = [
                'message' => 'Product updated',
                'rows' => $rows
            ];

            $body = json_encode($responseData);

            $response->getBody()->write($body);

            $this->logger->info('Product updated via API', [
                'product_id' => $id,
                'rows_affected' => $rows,
                'response' => $responseData
            ], 'product');

            return $response;
        }

        catch (\Exception $e) {
            $this->logger->error('Product update failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 'product');

            $response->getBody()->write(json_encode(['error' => 'Internal server error']));
            return $response->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, string $id): Response
    {
        try {
            $rows = $this->repository->delete($id);

//            $body = json_encode([
//                'message' => 'Product deleted',
//                'rows' => $rows
//            ]);

            $responseData = [
                'message' => 'Product deleted',
                'rows' => $rows
            ];

            $body = json_encode($responseData);

            $response->getBody()->write($body);

            $this->logger->info('Product deleted via API', [
                'product_id' => $id,
                'rows_affected' => $rows,
                'response' => $responseData
            ], 'product');

            return $response;
        }

        catch (\Exception $e) {
            $this->logger->error('Product deletion failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 'product');

            $response->getBody()->write(json_encode(['error' => 'Internal server error']));
            return $response->withStatus(500);
        }

    }

    public function getFile(Request $request, Response $response, string $id): Response
    {
        $product = $request->getAttribute('product');

        if (empty($product['file_path'])) {
            $this->logger->warning('Product file not found', ['product_id' => $id], 'product');
            return $response->withStatus(404);
        }

        $filePath = $this->fileService->getFile($product['file_path']);

        if (!$filePath) {
            $this->logger->error('Product file path invalid', [
                'product_id' => $id,
                'file_path' => $product['file_path']
            ], 'product');
            return $response->withStatus(404);
        }

        $fileContents = file_get_contents($filePath);
        $response->getBody()->write($fileContents);

        $this->logger->debug('Product file downloaded', [
            'product_id' => $id,
            'file_name' => $product['file_name']
        ], 'product');

        return $response->withHeader('Content-Type', $product['file_type'])
            ->withHeader('Content-Disposition', 'inline; filename="' . $product['file_name'] . '"');
    }

    public function updateFile(Request $request, Response $response, string $id): Response
    {
        $files = $request->getUploadedFiles();
        $existingProduct = $request->getAttribute('product');

        if (empty($files['file'])) {
            $this->logger->warning('No file provided for product file update', ['product_id' => $id], 'product');
            $response->getBody()->write(json_encode(['error' => 'No file provided']));
            return $response->withStatus(422);
        }

        try {
            // Delete old file if exists
            if (!empty($existingProduct['file_path'])) {
                $this->fileService->deleteFile($existingProduct['file_path']);

                $this->logger->info('Old product file deleted during file update', [
                    'product_id' => $id,
                    'old_file_path' => $existingProduct['file_path']
                ], 'product');
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

            $this->logger->info('Product file updated successfully', [
                'product_id' => $id,
                'file_data' => $fileData
            ], 'product');

            $response->getBody()->write(json_encode([
                'message' => 'File updated successfully'
            ]));

            return $response;

        } catch (\RuntimeException $e) {

            $this->logger->error('Product file update failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'file_name' => $files['file']->getClientFilename()
            ], 'product');

            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(422);
        }
    }
}
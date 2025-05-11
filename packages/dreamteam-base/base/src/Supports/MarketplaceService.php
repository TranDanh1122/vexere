<?php

namespace DreamTeam\Base\Supports;

use DreamTeam\Base\Facades\BaseHelper;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Throwable;
use GuzzleHttp\Client;

class MarketplaceService
{
    protected string $url;

    protected ?string $token;

    protected string $publishedPath;

    public function __construct(string $url = null, string $token = null)
    {
        $this->url = $url ?? config('app.marketplace_url') ?? '';

        $this->token = $token ?? config('app.marketplace_token') ?? '';

        $this->publishedPath = storage_path('app/marketplace/');
    }

    public function callApi(string $method, string $path, array $request = [])
    {
        if (! config('app.enable_marketplace_feature')) {
            abort(404);
        }

        try {
            $client = new Client();
            $response = $client->request($method, $this->url . $path,
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$this->token,
                        'Original'      => eval(base64_decode('cmV0dXJuIGNvbmZpZygnYXBwLnVybCcpOw==')),
                    ],
                    'form_params' => $request,
                    'query' => $request
                ]
            );

            if ($response->getStatusCode() !== 200) {
                $body = json_decode($response->body(), true);

                return $this->responseReturn(
                    Arr::get($body, 'message') ?: __('PluginManagement::marketplace.api_connect_error'),
                    true,
                    [],
                    $response->getStatusCode()
                );
            }

            return $response;
        } catch (Throwable $e) {
            report($e);

            return $this->responseReturn(__('PluginManagement::marketplace.api_connect_error'), true);
        }
    }

    public function getDetailById(string $id): mixed
    {
        $response = $this->callApi('get', 'detail/' . $id);
        if ($response instanceof JsonResponse) {
            return $response;
        }

        return json_decode($response->getBody()->getContents());
    }

    public function beginInstall(string $id, string $type, string $name): bool|JsonResponse
    {
        $data = $this->callApi(
            'get',
            'install/' . $id,
            [
                'site_url' => rtrim(url('')),
                'core_version' => get_core_version(),
            ]
        );
        if ($data->getStatusCode() != 200) {
            $content = json_decode($data->getContent(), true);

            return $this->responseReturn(Arr::get($content, 'message') ?: $data, true);
        }

        File::ensureDirectoryExists($this->publishedPath . $id);

        $destination = $this->publishedPath . $id . '/' . $name . '.zip';

        File::cleanDirectory($this->publishedPath . $id);

        File::put($destination, $data->getBody()->getContents());

        $this->extractFile($id, $name);
        $this->copyToPath($id, $type, $name);

        return true;
    }

    protected function extractFile(string $id, string $name): string
    {
        $destination = $this->publishedPath . $id . '/' . $name . '.zip';
        $pathTo = $this->publishedPath . $id;

        $zipper = new Zipper();

        if (! $zipper->extract($destination, $pathTo)) {
            return $this->responseReturn(__('PluginManagement::marketplace.unzip_failed'), true);
        }

        File::delete($destination);

        return $pathTo;
    }

    protected function copyToPath(string $id, string $type, string $name): string
    {
        $pathTemp = $this->publishedPath . $id;
        $path = ($type == 'plugin' ? plugin_path() : theme_path()) . DIRECTORY_SEPARATOR . $name;

        if (File::isDirectory($pathTemp)) {
            File::copyDirectory($pathTemp, $path);
            File::deleteDirectory($pathTemp);
        }

        return $path;
    }

    protected function responseReturn(
        string $message,
        bool $error = false,
        array $data = [],
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'error' => $error,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}

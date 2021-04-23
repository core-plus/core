<?php

namespace Core\Cdn\Adapter;

use Illuminate\Support\Arr;
use Core\Cdn\Refresh;
use GuzzleHttp\Client as HttpClient;
use Core\Contracts\Cdn\UrlGenerator as FileUrlGeneratorContract;

class Qiniu implements FileUrlGeneratorContract
{
    /**
     * The cdn domain.
     *
     * @var string
     */
    private $domain = null;

    /**
     * Build url sign.
     *
     * @var bool
     */
    private $sign = false;

    /**
     * Qiniu Access key.
     *
     * @var string
     */
    private $ak;

    /**
     * Qiniu Secret key.
     *
     * @var string
     */
    private $sk;

    private $expires = 3600;
    private $type = 'object';

    /**
     * Create the qiniu cdn adapter instance.
     *
     * @author GEO <dev@kaifa.me>
     */
    public function __construct()
    {
        $this->domain = config('cdn.generators.qiniu.domain');
        $this->sign = config('cdn.generators.qiniu.sign', false);
        $this->ak = config('cdn.generators.qiniu.ak');
        $this->sk = config('cdn.generators.qiniu.sk');
        $this->expires = config('cdn.generators.qiniu.expires', 3600);
        $this->type = config('cdn.generators.qiniu.type', 'object');
        $this->bucket = config('cdn.generators.qiniu.bucket');
    }

    /**
     * Build the filename url.
     *
     * @param string $filename
     * @param array $extra
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function url(string $filename, array $extra = []): string
    {
        return $this->validateImageAnd($filename, function (string $filename) use ($extra): string {
            $isImage = true; // The file is a image.

            return $this->make($filename, $extra, $isImage);
        });
    }

    /**
     * Refresh the cdn files and dirs.
     *
     * @param \Core\Cdn\Refresh $refresh
     * @return void
     * @author GEO <dev@kaifa.me>
     */
    public function refresh(Refresh $refresh)
    {
        if ($this->type === 'cdn') {
            return $this->refreshByCdn($refresh);
        }

        return $this->refreshByObject($refresh);
    }

    /**
     * 刷新 融合 CDN.
     *
     * @param \Core\Cdn\Refresh $refresh
     * @return void
     * @author GEO <dev@kaifa.me>
     */
    protected function refreshByCdn(Refresh $refresh)
    {
        $disk = app('filesystem')->disk(
            config('cdn.generators.filesystem.disk')
        );
        $files = array_map(function ($file) use ($disk) {
            return $disk->url($file);
        }, $refresh->getFiles());
        $dirs = array_map(function ($dir) use ($disk) {
            if (substr($dir, 0, -1) !== '/') {
                $dir .= '/';
            }

            return $disk->url($dir);
        }, $refresh->getDirs());

        $body = json_encode([
            'urls' => $files,
            'dirs' => $dirs,
        ]);

        $client = new HttpClient();
        $url = '/v2/tune/refresh';
        $token = $this->generateToken($url);

        $client->request('post', 'http://fusion.qiniuapi.com'.$url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'QBox '.$token,
            ],
            'body' => $body,
        ]);
    }

    /**
     * 删除 Qiniu Object Storage.
     *
     * @param \Core\Cdn\Refresh $refresh
     * @return void
     * @author GEO <dev@kaifa.me>
     */
    protected function refreshByObject(Refresh $refresh)
    {
        $client = new HttpClient();
        $files = [];

        foreach ($refresh->getDirs() as $dir) {
            $query = [
                'bucket' => $this->bucket,
                'limit' => 20,
                'prefix' => $dir,
            ];
            $url = sprintf('/list?%s', http_build_query($query));
            $token = $this->generateToken($url);

            $res = $client->request('GET', 'https://rsf.qiniu.com'.$url, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'QBox '.$token,
                ],
            ]);

            foreach (json_decode($res->getBody()->getContents(), false)->items ?? [] as $item) {
                array_push($files, sprintf('op=/delete/%s', $this->safeBase64Encode($this->bucket.':'.$item->key)));
            }
        }

        $url = '/batch';
        $token = $this->generateToken($url, $body = implode('&', $files));

        if ($files) {
            $client->request('post', 'https://rs.qiniu.com'.$url, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'QBox '.$token,
                ],
                'body' => $body,
            ]);
        }
    }

    /**
     * Make the filename type.
     *
     * @param string $filename
     * @param array $extra
     * @param bool $image
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    private function make(string $filename, array $extra = [], $image = false): string
    {
        if (! $image) {
            return $this->makeFile($filename);
        }

        return $this->makeImage($filename, $extra);
    }

    /**
     * Build file url.
     *
     * @param string $filename
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    private function makeFile(string $filename): string
    {
        return $this->makeToken(
            $this->domain.'/'.$filename
        );
    }

    /**
     * Build image url.
     *
     * @param string $filename
     * @param array $extra
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    private function makeImage(string $filename, array $extra = []): string
    {
        $width = max(0, intval(Arr::get($extra, 'width', 0)));
        $height = max(0, intval(Arr::get($extra, 'height', 0)));
        $quality = min(100, max(0, intval($extra['quality'] ?? 100)));
        $blur = max(0, intval($extra['blur'] ?? 0));
        $processor = $this->makeImageProcessor($width, $height, $quality, $blur);
        $url = sprintf('%s/%s?%s', $this->domain, $filename, $processor);

        return $this->makeToken($url);
    }

    /**
     * Make the private resource token.
     *
     * @param string $url
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    private function makeToken(string $url): string
    {
        if (! $this->sign) {
            return $url;
        }

        $deadline = time() + $this->expires;
        $url .= (strpos($url, '?') ? '&' : '?').'e='.$deadline;
        $hmac = hash_hmac('sha1', $url, $this->sk, true);
        $token = $this->ak.':'.$this->safeBase64Encode($hmac);

        return $url .= '&token='.$token;
    }

    protected function generateToken(string $data, string $body = ''): string
    {
        $data .= "\n".$body;
        $hmac = hash_hmac('sha1', $data, $this->sk, true);

        return $this->ak.':'.$this->safeBase64Encode($hmac);
    }

    protected function safeBase64Encode(string $data): string
    {
        return str_replace(['+', '/'], ['-', '_'], base64_encode($data));
    }

    /**
     * Build the image processor.
     *
     * @param int $width
     * @param int $height
     * @param int $quality
     * @param int $blur
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    private function makeImageProcessor(int $width, int $height, int $quality, int $blur): string
    {
        return sprintf('imageView2/2/w/%d/h/%d/q/%d|imageMogr2/blur/50x%d/quality/%d/', $width, $height, $quality, $blur, $quality);
    }

    /**
     * Validate is image.
     *
     * @param string $filename
     * @param callable $call
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    private function validateImageAnd(string $filename, callable $call): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($extension, $this->getSupportExtensions())) {
            return $call($filename);
        }

        return $this->make($filename);
    }

    /**
     * Get support make file extension.
     *
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    private function getSupportExtensions(): array
    {
        return ['psd', 'png', 'jpg', 'jpeg', 'webp', 'bmp', 'gif', 'tiff'];
    }
}

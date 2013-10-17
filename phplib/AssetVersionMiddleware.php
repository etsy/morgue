<?php

class AssetVersionMiddleware extends Slim_Middleware {
    public function call() {
        $version = null;

        $this->next->call();

        $res = $this->app->response();
        $body = $res->body();

        $body = preg_replace('!(<link [^>]*href="[^"]+\.css)"!', '\1?v=' . MORGUE_VERSION . '\2"', $body);
        $res->body($body);
    }
}

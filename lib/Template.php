<?php namespace Library;


use Error\Server;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

final class Template {
    private function __construct(){}

    private static Environment $twig;
    private static string $title_tpl;
    private static string $base_title;
    private static string $title = '';

    public static function init(string $template_dir, bool $cache = false, string $cache_dir = ''){
        $loader = new FilesystemLoader(DIR_TEMPLATE);

        $config = array('autoescape' => false);

        if ($cache) {
            $config['cache'] = DIR_CACHE;
        }

        self::$twig = new Environment($loader, $config);
    }
    public static function render($template, $data) {
        try {
            return self::$twig->render($template,$data);
        } catch (LoaderError $err) {
            throw new Server(
                'Ошибка загрузки шаблона twig: '.$err->getMessage(),
                true, $err->getPrevious()
            );
        } catch (RuntimeError $err) {
            throw new Server(
                'Ошибка выполнения в шаблоне twig: '.$err->getMessage(),
                true, $err->getPrevious()
            );
        } catch (SyntaxError $err) {
            throw new Server(
                'Ошибка синтаксиса в шаблоне twig: '.$err->getMessage(),
                true, $err->getPrevious()
            );
        }
    }

    public static function setTitleTemplate(string $title_tpl, string $base_title = ''){
        self::$title_tpl = $title_tpl;
        self::$base_title = $base_title;
    }
    public static function setTitle(string $title){
        self::$title = $title;
    }
    public static function getTitle(){
        return empty(self::$title) ? self::$base_title : sprintf(self::$title_tpl, self::$title);
    }

}

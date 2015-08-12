<?php

namespace luya\components;

use Yii;
use luya\helpers\Url;

/**
 * @todo see http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html#adding-rules-dynamically
 * @todo change to public $ruleConfig = ['class' => 'yii\web\UrlRule'];
 *
 * @author nadar
 */
class UrlManager extends \yii\web\UrlManager
{
    public $enablePrettyUrl = true;

    public $showScriptName = false;

    public $ruleConfig = ['class' => '\luya\base\UrlRule'];

    private $_contextNavItemId = false;

    public function parseRequest($request)
    {
        $route = parent::parseRequest($request);

        /**
         * @todo previous `!is_object(\yii::$app->composition)`can remove?`how to handle?
         */
        if (!(Yii::$app->has('composition'))) {
            return $route;
        }

        $composition = Yii::$app->composition->getFull();

        $length = strlen($composition);
        if (substr($route[0], 0, $length) == $composition) {
            $route[0] = substr($route[0], $length);
        }

        return $route;
    }

    public function addRules($rules, $append = true)
    {
        foreach ($rules as $key => $rule) {
            if (isset($rule['composition'])) {
                foreach ($rule['composition'] as $comp => $pattern) {
                    $rules[] = [
                        'pattern' => $pattern,
                        'route' => $comp.'/'.$rule['route'],
                    ];
                }
            }
        }

        return parent::addRules($rules, $append);
    }

    public function setContextNavItemId($navItemId)
    {
        $this->_contextNavItemId = $navItemId;
    }

    public function getContextNavItemId()
    {
        return $this->_contextNavItemId;
    }

    public function resetContext()
    {
        $this->_contextNavItemId = false;
    }

    public function createUrl($params)
    {
        if (!Yii::$app->has('composition')) {
            return parent::createUrl($params);
        }

        $links = Yii::$app->get('links', false);

        $composition = Yii::$app->composition->getFull();

        $originalParams = $params;

        $params[0] = $composition.$params[0];

        $response = parent::createUrl($params);

        // we have not found the composition rule matching against rules, so use the original params and try again!
        if (strpos($response, $params[0]) !== false) {
            $params = $originalParams;
            $response = parent::createUrl($params);
        } else {
            // now we have to remove the composition informations from the links to make a valid link parsing (read module)
            $params[0] = str_replace($composition, '', $params[0]);
        }

        $params = (array) $params;
        $moduleName = \luya\helpers\Url::fromRoute($params[0], 'module');

        if ($this->getContextNavItemId() && $links !== null) {
            $link = $links->findOneByArguments(['id' => (int) $this->getContextNavItemId()]);
            $this->resetContext();

            return preg_replace("/$moduleName/", $composition.$link['url'], $response, 1);
        }

        $context = false;

        if ($moduleName !== false) {
            $moduleObject = \yii::$app->getModule($moduleName);

            if (method_exists($moduleObject, 'setContext') && !empty($moduleObject->context)) {
                if ($links !== null) {
                    $context = true;
                    $options = $moduleObject->getContextOptions();
                    $link = $links->findOneByArguments(['id' => (int) $options['navItemId']]);
                    $response = preg_replace("/$moduleName/", $composition.$link['url'], $response, 1);
                }
            }
        }
        if (!$context) {
            // because the urlCreation of yii returns a realtive url we have to manually add the composition getFull() path.
            $baseUrl = yii::$app->request->baseUrl;
            if (empty($baseUrl)) {
                $response = Url::removeTrailing($composition).$response;
            } else {
                $response = str_replace($baseUrl, Url::trailing($baseUrl).Url::removeTrailing($composition), $response);
            }
        }

        return $response;
    }
}

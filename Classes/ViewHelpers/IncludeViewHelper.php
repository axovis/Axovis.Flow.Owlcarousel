<?php
namespace Axovis\Flow\Owlcarousel\ViewHelpers;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Resource\ResourceManager;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

class IncludeViewHelper extends AbstractViewHelper {
    /**
     * @var bool
     */
    public static $cssIncluded = false;

    /**
     * @var bool
     */
    public static $jsIncluded = false;

    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @param boolean $minified
     * @param string $include
     * @return string
     * @throws \Exception
     */
    public function render($minified = true,$include = 'remaining') {
        if(!in_array($include,array('all','css','js','remaining'))) {
            throw new \Exception('invalid include parameter. valid values are "remaining", "all", "css" and "js".');
        }

        if($include == 'remaining') {
            if(self::$cssIncluded && self::$jsIncluded) {
                return '';
            } else if(!self::$cssIncluded && !self::$jsIncluded) {
                $include = 'all';
            } else if(!self::$cssIncluded) {
                $include = 'css';
            } else {
                $include = 'js';
            }
        } else if($include == 'all' && (self::$cssIncluded || self::$jsIncluded)) {
            throw new \Exception("Some Owlcarousel's dependencies are already included.");
        } else if($include == 'css' && self::$cssIncluded) {
            throw new \Exception("Owlcarousel's CSS is already included.");
        } else if($include == 'js' && self::$jsIncluded) {
            throw new \Exception("Owlcarousel's JavaScript is already included.");
        }

        $return = '';
        if($include == 'css' || $include == 'all') {
            $css = $this->resourceManager->getPublicPackageResourceUri('Axovis.Flow.Owlcarousel', 'Styles/owl.carousel.css');
            $return .= '<link rel="stylesheet" type="text/css" href="' . $css . '" />';
            self::$cssIncluded = true;
        }
        if($include == 'js' || $include == 'all') {
            $js = $this->resourceManager->getPublicPackageResourceUri('Axovis.Flow.Owlcarousel', 'JavaScript/owl.carousel' . ($minified ? '.min' : '') . '.js');
            $return .= '<script type="text/javascript" src="' . $js . '"></script>';
            self::$jsIncluded = true;
        }

        return $return;
    }
}
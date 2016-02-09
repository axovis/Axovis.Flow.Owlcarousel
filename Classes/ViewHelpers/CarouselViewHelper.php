<?php
namespace Axovis\Flow\Owlcarousel\ViewHelpers;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Resource\ResourceManager;
use TYPO3\Flow\Resource\Resource;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\Media\Domain\Model\Asset;

class CarouselViewHelper extends AbstractViewHelper {
    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @param array $items array of Resources,Assets or strings (uri)
     * @param string $id of the carousel instance
     * @param string $class class attribute of carousel element
     * @param bool $autoinclude include scripts and styles if not already done
     * @param int $numItems number of items to display at once
     * @param bool $itemsScaleUp scale up carousel items
     * @param bool $singleItem always display a single item
     * @param bool $showNavigation show carousel navigation
     * @param bool $pagination enable pagination
     * @param int $paginationSpeed speed of the pagination
     * @param bool $paginationNumbers show pagination numbers
     * @param bool $rewindNavigation rewind navigation on end
     * @param bool $autoplay enable autoplay
     * @param int $autoplaySpeed speed of autoplay
     * @param bool $pauseOnHover pause autoplay on hover
     * @param bool $loop loop animation instead of rewind
     * @param bool $isResponsive enable responsive design
     * @param array<string,int> $responsiveConfig responsive configuration array("{minScreenSize}" => [numElements],...))
     *
     * @return string
     */
    public function render($items,$id = null,$class = null,$autoinclude = true,$numItems = 5,$itemsScaleUp = true,$singleItem = true,$showNavigation = true,$pagination = true,$paginationSpeed = 800,$paginationNumbers = true,$rewindNavigation = true,$autoplay = true,$autoplaySpeed = 200,$pauseOnHover = true,$loop = true,$isResponsive = true,$responsiveConfig = array("0" => 1, "479" => 2, "768" => 3, "1199" => 5)) {
        if($id == null) {
            $id = 'oc' . md5(microtime());
        }
        if($class == null) {
            $class = 'owl-carousel';
        } else {
            $class .= ' owl-carousel';
        }

        //build config array
        $config = array();
        $config['items'] = $numItems;
        $config['itemsScaleUp'] = $itemsScaleUp;
        $config['singleItem'] = $singleItem;
        $config['nav'] = $showNavigation;
        $config['pagination'] = $pagination;
        $config['paginationSpeed'] = $paginationSpeed;
        $config['paginationNumbers'] = $paginationNumbers;
        $config['rewindNav'] = $rewindNavigation;
        $config['autoplay'] = $autoplay;
        $config['autoplaySpeed'] = $autoplaySpeed;
        $config['autoplayHoverPause'] = $pauseOnHover;
        $config['loop'] = $loop;
        $config['responsiveClass'] = $isResponsive;

        //include dependencies if necessary
        $includeContent = '';
        if($autoinclude) {
            $viewHelper = $this->objectManager->get('Axovis\Flow\Owlcarousel\ViewHelpers\IncludeViewHelper');
            $includeContent = $viewHelper->render(true,'remaining');
        }

        //build items content
        $itemsContent = '';
        foreach($items as $item) {
            $title = '';
            $caption = '';
            if($item !== null && $item instanceof Resource) {
                $uri = $this->resourceManager->getPublicPersistentResourceUri($item);
            } else if($item !== null && $item instanceof Asset) {
                $uri = $this->resourceManager->getPublicPersistentResourceUri($item->getResource());
                $title = $item->getTitle();
                $caption = $item->getCaption();
            } else if($item !== null && is_string($item)) {
                $uri = $item;
            } else {
                $title = 'Dummy Image';
                $uri = $this->resourceManager->getPublicPackageResourceUri('Axovis.Flow.Owlcarousel', 'Images/dummy-image.png');
            }
            $itemsContent .= '
                <div class="item">
                    <div>
                        <img src="' . $uri . '" title="' . $title . '" alt="' . $title . '" />
                        <div class="carousel-caption">
                            ' . $caption . '
                        </div>
                    </div>
                </div>
            ';
        }

        //build responsive config content
        $responsiveConfigContent = '';
        if($isResponsive) {
            $responsiveConfigContent = 'config.responsive = {};';
            foreach($responsiveConfig as $screen => $numItems) {
                $responsiveConfigContent .= '
                    config.responsive["' . $screen . '"] = {};
                    config.responsive["' . $screen . '"].items = ' . $numItems . ';
                ';
            }
        }

        return '
            ' . $includeContent . '
            <div id="' . $id . '" class="' . $class . '">
                ' . $itemsContent . '
            </div>
            <script type="text/javascript">
                $(document).ready(function(){
                    var config = ' . json_encode($config) . ';
                    ' . $responsiveConfigContent . '

                    $("#' . $id . '").owlCarousel(config);
                });
            </script>
        ';
    }
}
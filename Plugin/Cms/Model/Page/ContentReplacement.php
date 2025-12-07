<?php
namespace Graycore\CmsAiBuilder\Plugin\Cms\Model\Page;

use Magento\Cms\Model\Page;
use Graycore\CmsAiBuilder\Api\PageAiRepositoryInterface;
use Graycore\CmsAiBuilder\Service\AiContentRenderer;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class ContentReplacement
{
    /**
     * @var PageAiRepositoryInterface
     */
    private $pageAiRepository;

    /**
     * @var AiContentRenderer
     */
    private $aiContentRenderer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param PageAiRepositoryInterface $pageAiRepository
     * @param AiContentRenderer $aiContentRenderer
     * @param LoggerInterface $logger
     */
    public function __construct(
        PageAiRepositoryInterface $pageAiRepository,
        AiContentRenderer $aiContentRenderer,
        LoggerInterface $logger
    ) {
        $this->pageAiRepository = $pageAiRepository;
        $this->aiContentRenderer = $aiContentRenderer;
        $this->logger = $logger;
    }

    /**
     * After get content
     *
     * @param Page $subject
     * @param string|null $result
     * @return string|null
     */
    public function afterGetContent(Page $subject, $result)
    {
        // If content is not empty, return it as is
        if (!empty(trim((string)$result))) {
            return $result;
        }

        try {
            $pageId = $subject->getId();
            if (!$pageId) {
                return $result;
            }

            $pageAi = $this->pageAiRepository->getByPageId($pageId);
            $json = $pageAi->getAiSchemaJson();

            if ($json) {
                return $this->aiContentRenderer->render($json);
            }
        } catch (NoSuchEntityException $e) {
            // No AI content found for this page, ignore
        } catch (\Exception $e) {
            $this->logger->error('Error rendering AI content for page ' . $subject->getId() . ': ' . $e->getMessage());
        }

        return $result;
    }
}

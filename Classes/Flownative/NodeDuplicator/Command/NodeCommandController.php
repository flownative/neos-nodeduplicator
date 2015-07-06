<?php
namespace Flownative\NodeDuplicator\Command;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;
use TYPO3\Neos\Domain\Service\ContentContextFactory;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TYPO3CR\Domain\Service\Context;

/**
 *
 */
class NodeCommandController extends CommandController {

	/**
	 * @Flow\Inject
	 * @var ContentContextFactory
	 */
	protected $contextFactory;

	/**
	 * Adopt the given starting point and all child nodes recursively from the given dimension to the target dimensions.
	 *
	 * Dimensions can be given as a string with the format
	 * <dimensionName>=<dimensionValue>[,<optionallyMultipleValues>][&<nextDimensionName=<dimensionValue>]
	 *
	 * @param string $startingPoint
	 * @param string $fromDimensions
	 * @param string $toDimensions
	 * @param string $workspace
	 */
	public function adoptCommand($startingPoint, $fromDimensions, $toDimensions, $workspace = 'live') {
		$fromDimensionArray = $this->contextFactory->parseDimensionValueStringToArray($fromDimensions);
		$toDimensionArray = $this->contextFactory->parseDimensionValueStringToArray($toDimensions);

		$sourceContext = $this->createContext($workspace, $fromDimensionArray);
		$targetContext = $this->createContext($workspace, $toDimensionArray);

		if (preg_match(\TYPO3\Flow\Validation\Validator\UuidValidator::PATTERN_MATCH_UUID, $startingPoint)) {
			$startingPointNode = $sourceContext->getNodeByIdentifier($startingPoint);
		} else {
			$startingPointNode = $sourceContext->getNode($startingPoint);
		}

		if ($startingPointNode === NULL) {
			$this->outputLine('Could not find give starting point to adopt nodes.');
			$this->quit(1);
		}

		$this->adoptToTargetContext($startingPointNode, $targetContext);

		$this->outputLine('');
		$this->outputLine('All nodes have been adopted. Done...');
		$this->quit(0);
	}

	/**
	 * Create a context with the given workspace and dimensions
	 *
	 * @param string $workspaceName
	 * @param array $dimensions
	 * @return Context
	 */
	protected function createContext($workspaceName, array $dimensions) {
		return $this->contextFactory->create(array(
			'workspaceName' => $workspaceName,
			'dimensions' => $dimensions
		));
	}

	/**
	 * Adopt recursively to the targetContext.
	 *
	 * @param NodeInterface $startingPointNode
	 * @param Context $targetContext
	 */
	protected function adoptToTargetContext(NodeInterface $startingPointNode, Context $targetContext) {
		$targetContext->adoptNode($startingPointNode);
		$this->outputLine(sprintf('Adopted Node "%s" to new dimensions.', $startingPointNode->getPath()));
		foreach ($startingPointNode->getChildNodes() as $childNode) {
			$this->adoptToTargetContext($childNode, $targetContext);
		}
	}
}
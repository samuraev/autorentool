<?php
/**
 * Class for generating xml objects.
 *
 * @author Alexey Zamuraev
 * @version 0.05
 */

namespace Autorentool\CoreBundle\GenerateTasks;

use Autorentool\CoreBundle\Entity\TAssessmentItem;
use Autorentool\CoreBundle\Entity\TRelated;
use Autorentool\CoreBundle\Entity\TSupport;
use Doctrine\DBAL\Types\JsonArrayType;
use Doctrine\ORM\EntityManager;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\Response;

class GenerateXML
{
    private $assessmentRoot;

    private function GenerateAssessmentItemSingleChoice(TAssessmentItem $assessmentItemObj){

        $assessmentItem = $this->assessmentRoot->addChild( 'assessmentItem' );

        $assessmentItem->addAttribute('id', $assessmentItemObj->getUuid());
        $assessmentItem->addAttribute('creationTimestamp', $assessmentItemObj->getCreationTimestamp());
        $assessmentItem->addAttribute('categoryTags', $assessmentItemObj->getCategoryTagsString());
        $assessmentItem->addAttribute('identifier', $assessmentItemObj->getIdentifier());
        $assessmentItem->addAttribute('title', $assessmentItemObj->getTittle());
        $assessmentItem->addAttribute('adaptive', $assessmentItemObj->getAdaptiveString());
        $assessmentItem->addAttribute('timeDependent', $assessmentItemObj->getTimeDependentString());

        $responseDeclarationObj  = $assessmentItemObj->getResponseDeclaration();
        $responseDeclaration = $assessmentItem->addChild('responseDeclaration');
        $responseDeclaration->addAttribute('identifier', $responseDeclarationObj->getIdentifier());
        $responseDeclaration->addAttribute('cardinality', $responseDeclarationObj->getCardinality());
        $responseDeclaration->addAttribute('baseType', $responseDeclarationObj->getBaseType());

        $correctResponseObj = $responseDeclarationObj->getCorrectResponce();
        $correctResponse = $responseDeclaration->addChild('correctResponse');

        $valuesObj = $correctResponseObj->getValues();
        foreach ($valuesObj as $valueObj) {
            $correctResponse->addChild('value', $valueObj->getValue());
        }

        $itemBodyObj = $assessmentItemObj->getItemBody();
        $itemBody = $assessmentItem->addChild('itemBody');
        $itemBody->addChild('p', $itemBodyObj->getParagraph());

        if ($itemBodyObj->getImgSrc() !== null) {
            $p = $itemBody->addChild('p');
            $img = $p->addChild('img');
            $img->addAttribute('src', $itemBodyObj->getImgSrc());
        }

        $choiceInteractionObj = $itemBodyObj->getChoiceInteraction();
        $choiceInteraction = $itemBody->addChild('choiceInteraction');
        $choiceInteraction->addAttribute('responseIdentifier', $choiceInteractionObj->getResponseIdentifier());
        $choiceInteraction->addAttribute('shuffle', $choiceInteractionObj->getShuffleString());
        $choiceInteraction->addAttribute('maxChoices', $choiceInteractionObj->getMaxChoices());

        $choiceInteraction->addChild('prompt');

        $simpleChoicesObj = $choiceInteractionObj->getSimpleChoices();
        foreach ($simpleChoicesObj as $simpleChoiceObj) {
            $simpleChoice = $choiceInteraction->addChild('simpleChoice', $simpleChoiceObj->getCaption());
            $simpleChoice->addAttribute('identifier', $simpleChoiceObj->getIdentifier());

            if ($simpleChoiceObj->getImgSrc() !== null) {
                $img = $simpleChoice->addChild('img');
                $img->addAttribute('src', $simpleChoiceObj->getImgSrc());
            }
        }
    }

    private function GenerateAssessmentItemMultipleChoice(TAssessmentItem $assessmentItemObj){

        $assessmentItem = $this->assessmentRoot->addChild( 'assessmentItem' );

        $assessmentItem->addAttribute('id', $assessmentItemObj->getUuid());
        $assessmentItem->addAttribute('creationTimestamp', $assessmentItemObj->getCreationTimestamp());
        $assessmentItem->addAttribute('categoryTags', $assessmentItemObj->getCategoryTagsString());
        $assessmentItem->addAttribute('identifier', $assessmentItemObj->getIdentifier());
        $assessmentItem->addAttribute('title', $assessmentItemObj->getTittle());
        $assessmentItem->addAttribute('adaptive', $assessmentItemObj->getAdaptiveString());
        $assessmentItem->addAttribute('timeDependent', $assessmentItemObj->getTimeDependentString());

        $responseDeclarationObj  = $assessmentItemObj->getResponseDeclaration();
        $responseDeclaration = $assessmentItem->addChild('responseDeclaration');
        $responseDeclaration->addAttribute('identifier', $responseDeclarationObj->getIdentifier());
        $responseDeclaration->addAttribute('cardinality', $responseDeclarationObj->getCardinality());
        $responseDeclaration->addAttribute('baseType', $responseDeclarationObj->getBaseType());

        $correctResponseObj = $responseDeclarationObj->getCorrectResponce();
        $correctResponse = $responseDeclaration->addChild('correctResponse');

        $valuesObj = $correctResponseObj->getValues();
        foreach ($valuesObj as $valueObj) {
            $correctResponse->addChild('value', $valueObj->getValue());
        }

        $mappingObj = $responseDeclarationObj->getMapping();
        $mapping = $responseDeclaration->addChild('mapping');
        $mapping->addAttribute('lowerBound', $mappingObj->getLowerBound());
        $mapping->addAttribute('upperBound', $mappingObj->getUpperBound());
        $mapping->addAttribute('defaultValue', $mappingObj->getDefaultValue());

        $mappingEntriesObj = $mappingObj->getMapEntry();
        foreach ($mappingEntriesObj as $mappingEntryObj) {
            $mapEntry = $mapping->addChild('mapEntry');
            $mapEntry->addAttribute('mapKey', $mappingEntryObj->getMapKey());
            $mapEntry->addAttribute('mappedValue', $mappingEntryObj->getMappedValue());
        }

        $itemBodyObj = $assessmentItemObj->getItemBody();
        $itemBody = $assessmentItem->addChild('itemBody');
        $itemBody->addChild('p', $itemBodyObj->getParagraph());

        if ($itemBodyObj->getImgSrc() !== null) {
            $p = $itemBody->addChild('p');
            $img = $p->addChild('img');
            $img->addAttribute('src', $itemBodyObj->getImgSrc());
        }

        $choiceInteractionObj = $itemBodyObj->getChoiceInteraction();
        $choiceInteraction = $itemBody->addChild('choiceInteraction');
        $choiceInteraction->addAttribute('responseIdentifier', $choiceInteractionObj->getResponseIdentifier());
        $choiceInteraction->addAttribute('shuffle', $choiceInteractionObj->getShuffleString());
        $choiceInteraction->addAttribute('maxChoices', $choiceInteractionObj->getMaxChoices());

        $choiceInteraction->addChild('prompt');

        $simpleChoicesObj = $choiceInteractionObj->getSimpleChoices();
        foreach ($simpleChoicesObj as $simpleChoiceObj) {
            $simpleChoice = $choiceInteraction->addChild('simpleChoice', $simpleChoiceObj->getCaption());
            $simpleChoice->addAttribute('identifier', $simpleChoiceObj->getIdentifier());

            if ($simpleChoiceObj->getImgSrc() !== null) {
                $img = $simpleChoice->addChild('img');
                $img->addAttribute('src', $simpleChoiceObj->getImgSrc());
            }
        }

        $assessmentItem->addChild('responseProcessing');
    }

    private function GenerateAssessmentItemHotspot(TAssessmentItem $assessmentItemObject){

        $assessmentItem = $this->assessmentRoot->addChild( 'assessmentItem' );

        $assessmentItem->addAttribute('id', $assessmentItemObject->getUuid());
        $assessmentItem->addAttribute('creationTimestamp', $assessmentItemObject->getCreationTimestamp());
        $assessmentItem->addAttribute('categoryTags', $assessmentItemObject->getCategoryTagsString());
        $assessmentItem->addAttribute('identifier', $assessmentItemObject->getIdentifier());
        $assessmentItem->addAttribute('title', $assessmentItemObject->getTittle());
        $assessmentItem->addAttribute('adaptive', $assessmentItemObject->getAdaptiveString());
        $assessmentItem->addAttribute('timeDependent', $assessmentItemObject->getTimeDependentString());

        $responseDeclarationObject  = $assessmentItemObject->getResponseDeclaration();
        $responseDeclaration = $assessmentItem->addChild('responseDeclaration');
        $responseDeclaration->addAttribute('identifier', $responseDeclarationObject->getIdentifier());
        $responseDeclaration->addAttribute('cardinality', $responseDeclarationObject->getCardinality());
        $responseDeclaration->addAttribute('baseType', $responseDeclarationObject->getBaseType());

        $correctResponseObject = $responseDeclarationObject->getCorrectResponce();
        $correctResponse = $responseDeclaration->addChild('correctResponse');

        $valueObjects = $correctResponseObject->getValues();
        foreach ($valueObjects as $valueObject) {
            $correctResponse->addChild('value', $valueObject->getValue());
        }

        $areaMappingObject = $responseDeclarationObject->getAreaMapping();
        $areaMapping = $responseDeclaration->addChild('areaMapping');
        $areaMapping->addAttribute('defaultValue', $areaMappingObject->getDefaultValue());

        $areaMapEntryObjects = $areaMappingObject->getAreaMapEntry();
        foreach ($areaMapEntryObjects as $areaMapEntryObject) {
            $areaMapEntry = $areaMapping->addChild('areaMapEntry');
            $areaMapEntry->addAttribute('shape', $areaMapEntryObject->getShape());
            $areaMapEntry->addAttribute('coords', $areaMapEntryObject->getCoords());
            $areaMapEntry->addAttribute('mappedValue', $areaMapEntryObject->getMappedValue());
        }

        $itemBodyObj = $assessmentItemObject->getItemBody();
        $itemBody = $assessmentItem->addChild('itemBody');
        $itemBody->addChild('p', $itemBodyObj->getParagraph());

        $hotspotInteractionObject = $itemBodyObj->getHotspotInteraction();
        $hotspotInteraction = $itemBody->addChild('positionObjectStage');

        $outerObjectObject = $hotspotInteractionObject->getOuterObject();
        $outerObject = $hotspotInteraction->addChild('object');
        $outerObject->addAttribute('type', $outerObjectObject->getType());
        $outerObject->addAttribute('data', $outerObjectObject->getData());
        $outerObject->addAttribute('width', $outerObjectObject->getWidth());
        $outerObject->addAttribute('height', $outerObjectObject->getHeight());

        $hotspotInteraction = $hotspotInteraction->addChild('positionObjectInteraction');
        $hotspotInteraction->addAttribute('responseIdentifier', $hotspotInteractionObject->getResponseIdentifier());
        $hotspotInteraction->addAttribute('maxChoices', $hotspotInteractionObject->getMaxChoices());

        $innerObjectObjects = $hotspotInteractionObject->getInnerObject();
        foreach ($innerObjectObjects as $innerObjectObject) {
            $innerObject = $hotspotInteraction->addChild('object');
            $innerObject->addAttribute('type', $innerObjectObject->getType());
            $innerObject->addAttribute('data', $innerObjectObject->getData());
            $innerObject->addAttribute('width', $innerObjectObject->getWidth());
            $innerObject->addAttribute('height', $innerObjectObject->getHeight());
        }

        $assessmentItem->addChild('responseProcessing');
    }

    public function GenerateAssessmentStandartXML(array $tasks) {

        $this->assessmentRoot = new SimpleXMLElement( "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                                                             <!DOCTYPE assessmentTest SYSTEM \"dtd_standard.dtd\">
                                                             <assessmentTest></assessmentTest>" );
        foreach ($tasks as $task) {
            switch ($task['identifier']) {
                case 'choice':
                    $this->GenerateAssessmentItemSingleChoice($task['assessmentItem']);
                    break;
                case 'choiceMultiple':
                    $this->GenerateAssessmentItemMultipleChoice($task['assessmentItem']);
                    break;
                case 'positionObjects':
                    $this->GenerateAssessmentItemHotspot($task['assessmentItem']);
                    break;
            }
        }

        return $this->assessmentRoot->asXML();
    }

    public function GenerateAssessmentRelatedXML(array $related) {

        $this->assessmentRoot = new SimpleXMLElement( "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                                                                 <!DOCTYPE relatedAssessments SYSTEM \"dtd_related.dtd\">
                                                                 <relatedAssessments></relatedAssessments>" );
        foreach ($related as $item) {
            $this->GenerateGroupRelatedXML($item);
        }

        return $this->assessmentRoot->asXML();
    }

    private function GenerateGroupRelatedXML(TRelated $relatedObject) {

        $group = $this->assessmentRoot->addChild( 'group' );
        $group->addAttribute('id', $relatedObject->getUuid());
        $group->addAttribute('creationTimestamp', $relatedObject->getCreationTimestamp());
        $group->addAttribute('categoryTags', $relatedObject->getCategoryTags());
        $group->addAttribute('title', $relatedObject->getTittle());
        //$group->addAttribute('shuffle', 'false');

        $itemsObjects = $relatedObject->getGroupItems();

        foreach ($itemsObjects as $itemsObject) {
            $group->addChild('item', $itemsObject->getAssessmentUuid());
        }
    }

    public function GenerateAssessmentExtendedXML(array $tasks) {

        $this->assessmentRoot = new SimpleXMLElement( "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                                                             <!DOCTYPE assessmentTest SYSTEM \"dtd_extended.dtd\">
                                                             <assessmentTest></assessmentTest>" );
        foreach ($tasks as $task) {
            switch ($task['identifier']) {
                case 'table':
                    $this->GenerateAssessmentItemTable($task['assessmentItem']);
                    break;
                case 'dragndropTable':
                    $this->GenerateAssessmentItemTableOrder($task['assessmentItem']);
                    break;
            }
        }

        return $this->assessmentRoot->asXML();
    }

    private function GenerateAssessmentItemTable(TAssessmentItem $assessmentItemObject){

        $assessmentItem = $this->assessmentRoot->addChild( 'assessmentItem' );

        $assessmentItem->addAttribute('id', $assessmentItemObject->getUuid());
        $assessmentItem->addAttribute('creationTimestamp', $assessmentItemObject->getCreationTimestamp());
        $assessmentItem->addAttribute('categoryTags', $assessmentItemObject->getCategoryTagsString());
        $assessmentItem->addAttribute('identifier', $assessmentItemObject->getIdentifier());
        $assessmentItem->addAttribute('title', $assessmentItemObject->getTittle());
        $assessmentItem->addAttribute('adaptive', $assessmentItemObject->getAdaptiveString());
        $assessmentItem->addAttribute('timeDependent', $assessmentItemObject->getTimeDependentString());

        $responseDeclarationObject  = $assessmentItemObject->getResponseDeclaration();
        $responseDeclaration = $assessmentItem->addChild('responseDeclaration');
        $responseDeclaration->addAttribute('identifier', $responseDeclarationObject->getIdentifier());
        $responseDeclaration->addAttribute('baseType', $responseDeclarationObject->getBaseType());

        $correctResponseObject = $responseDeclarationObject->getCorrectResponce();
        $correctResponse = $responseDeclaration->addChild('correctResponse');

        $valuesObjects = $correctResponseObject->getValues();
        foreach ($valuesObjects as $valueObject) {
            $value = $correctResponse->addChild('value', $valueObject->getValue());
            $value->addAttribute('cellIdentifier', $valueObject->getCellIdentifier());
        }

        $itemBodyObject = $assessmentItemObject->getItemBody();
        $itemBody = $assessmentItem->addChild('itemBody');
        $itemBody->addChild('p', $itemBodyObject->getParagraph());

        if ($itemBodyObject->getImgSrc() !== null) {
            $p = $itemBody->addChild('p');
            $img = $p->addChild('img');
            $img->addAttribute('src', $itemBodyObject->getImgSrc());
        }


        $tableInteractionObject = $itemBodyObject->getTableInteraction();
        $tableInteraction = $itemBody->addChild('tableInteraction');
        $tableInteraction->addAttribute('responseIdentifier', $tableInteractionObject->getResponseIdentifier());

        $tableInteraction->addChild('prompt');

        $table = $tableInteraction->addChild('table');
        $tableObject = $tableInteractionObject->getTable();

        $rowObjects = $tableObject->getRow();
        foreach ($rowObjects as $rowObject) {
            $cellObjects = $rowObject->getCell();
            $row = $table->addChild('row');
            foreach ($cellObjects as $cellObject) {

                if ($cellObject->getWriteable() === true) {
                    $cell = $row->addChild('cell');
                    $cell->addAttribute('cellIdentifier', $cellObject->getCellIdentifier());
                } else {

                    if ($cellObject->getValue() != NULL) {
                        $cell = $row->addChild('cell', $cellObject->getValue());
                    } else {
                        $cell = $row->addChild('cell');
                    }

                    if ($cellObject->getHead() != NULL) {
                        if ($cellObject->getHead() === true) {
                            $cell->addAttribute('head', $cellObject->getHeadString());
                        }
                    }

                    if ($cellObject->getWriteable() === false) {
                        $cell->addAttribute('writeable', $cellObject->getWriteableString());
                    }
                }
            }
        }
    }

    private function GenerateAssessmentItemTableOrder(TAssessmentItem $assessmentItemObject){
        $assessmentItem = $this->assessmentRoot->addChild( 'assessmentItem' );

        $assessmentItem->addAttribute('id', $assessmentItemObject->getUuid());
        $assessmentItem->addAttribute('creationTimestamp', $assessmentItemObject->getCreationTimestamp());
        $assessmentItem->addAttribute('categoryTags', $assessmentItemObject->getCategoryTagsString());
        $assessmentItem->addAttribute('identifier', $assessmentItemObject->getIdentifier());
        $assessmentItem->addAttribute('title', $assessmentItemObject->getTittle());
        $assessmentItem->addAttribute('adaptive', $assessmentItemObject->getAdaptiveString());
        $assessmentItem->addAttribute('timeDependent', $assessmentItemObject->getTimeDependentString());

        $itemBodyObject = $assessmentItemObject->getItemBody();
        $itemBody = $assessmentItem->addChild('itemBody');
        $itemBody->addChild('p', $itemBodyObject->getParagraph());

        if ($itemBodyObject->getImgSrc() !== null) {
            $p = $itemBody->addChild('p');
            $img = $p->addChild('img');
            $img->addAttribute('src', $itemBodyObject->getImgSrc());
        }

        $dragTableInteractionObject = $itemBodyObject->getDragInteraction();
        $dragTableInteraction = $itemBody->addChild('dragInteraction');
        $dragTableInteraction->addAttribute('mode', $dragTableInteractionObject->getMode());
        $dragTableInteraction->addChild('prompt');

        $table = $dragTableInteraction->addChild('table');
        $tableObject = $dragTableInteractionObject->getDragTable();

        $rowObjects = $tableObject->getRow();
        foreach ($rowObjects as $rowObject) {

            $cellObjects = $rowObject->getCell();
            $row = $table->addChild('row');
            foreach ($cellObjects as $cellObject) {
                if ($cellObject->getHeadString() === "true") {
                    $cell = $row->addChild('cell', $cellObject->getValue());
                    $cell->addAttribute('head', $cellObject->getHeadString());

                    if ($cellObject->getColumnIdentifier()) {
                        $cell->addAttribute('columnIdentifier', $cellObject->getColumnIdentifier());
                    }

                    if ($cellObject->getRowIdentifier()) {
                        $cell->addAttribute('columnIdentifier', $cellObject->getRowIdentifier());
                    }
                } else {
                    $row->addChild('cell');
                }
            }
        }

        $dragItems = $dragTableInteraction->addChild('dragItems');
        $dragItemsObjects = $dragTableInteractionObject->getDragItem();
        foreach ($dragItemsObjects as $dragItemsObject) {
            $dragItem = $dragItems->addChild('dragItem', $dragItemsObject->getValue());
            $dragItem->addAttribute('columnIdentifier', $dragItemsObject->getIdentifier());
        }
    }

    public function GenerateAssessmentSupportsXML(array $supports) {

        $this->assessmentRoot = new SimpleXMLElement( "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                                                             <!DOCTYPE supportAssessments SYSTEM \"dtd_support.dtd\">
                                                             <supportAssessments></supportAssessments>" );
        foreach ($supports as $supportObject) {
            $this->GenerateAssessmentSupportXML($supportObject);
        }

        return $this->assessmentRoot->asXML();
    }

    public function GenerateAssessmentSupportXML($supportObject) {
            $support = $this->assessmentRoot->addChild( 'support' );
            $support->addAttribute('id', $supportObject->getUuid());
            $support->addAttribute('assessmentId', $supportObject->getAssessmentUuid());
            $support->addAttribute('creationTimestamp', $supportObject->getCreationTimestamp());
            $support->addAttribute('identifier', $supportObject->getIdentifier());

            if ($supportObject->getSupportTextbox() != null) {

                $support->addChild('textBox', $supportObject->getSupportTextbox()->getTextboxContent());

            } elseif ($supportObject->getSupportSelection() != null) {

                $support->addChild('prompt');
                $selection = $support->addChild('selection');
                $supportSelectionItemObject = $supportObject->getSupportSelection()->getSelectionItem();
                foreach ($supportSelectionItemObject as $selectionItemObject) {
                    $selection->addChild('select', $selectionItemObject->getSelectValue());
                }

            } elseif ($supportObject->getSupportMedia() != null) {
                $support->addChild('prompt', $supportObject->getSupportMedia()->getPrompt());


                if ($supportObject->getIdentifier() === 'video') {
                    $madiaChild = $support->addChild('vid');
                    $madiaChild->addAttribute('src', $supportObject->getSupportMedia()->getMediaSource());
                } elseif ($supportObject->getIdentifier() === 'image') {
                    $madiaChild = $support->addChild('img');
                    $madiaChild->addAttribute('src', $supportObject->getSupportMedia()->getMediaSource());
                }
            } elseif ($supportObject->getSupportTable() != null) {

                $support->addChild('prompt');

                $table = $support->addChild('table');
                $tableObject = $supportObject->getSupportTable();

                $rowObjects = $tableObject->getRow();
                foreach ($rowObjects as $rowObject) {
                    $cellObjects = $rowObject->getCell();
                    $row = $table->addChild('row');
                    foreach ($cellObjects as $cellObject) {
                        $row->addChild('cell', $cellObject->getValue());
                    }
                }
            }

        return $this->assessmentRoot->asXML();
    }

    public function GenerateAssessmentXML($identifier, $item) {

        if ($identifier === "choice" or $identifier === "choiceMultiple" or $identifier === "positionObjects") {
            $this->assessmentRoot = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                                                             <!DOCTYPE assessmentTest SYSTEM \"dtd_standard.dtd\">
                                                             <assessmentTest></assessmentTest>");
        } elseif ($identifier === "table" or $identifier === "dragndropTable") {
            $this->assessmentRoot = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                                                            <!DOCTYPE assessmentTest SYSTEM \"dtd_extended.dtd\">
                                                            <assessmentTest></assessmentTest>");
        } elseif ($identifier === "support") {
            $this->assessmentRoot = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                                                            <!DOCTYPE supportAssessments SYSTEM \"dtd_support.dtd\">
                                                            <supportAssessments></supportAssessments>");
        } elseif ($identifier === "related") {
            $this->assessmentRoot = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                                                             <!DOCTYPE relatedAssessments SYSTEM \"dtd_related.dtd\">
                                                             <relatedAssessments></relatedAssessments>");
        }

        switch ($identifier) {
            case 'choice':
                $this->GenerateAssessmentItemSingleChoice($item);
                break;
            case 'choiceMultiple':
                $this->GenerateAssessmentItemMultipleChoice($item);
                break;
            case 'positionObjects':
                $this->GenerateAssessmentItemHotspot($item);
                break;
            case 'table':
                $this->GenerateAssessmentItemTable($item);
                break;
            case 'dragndropTable':
                $this->GenerateAssessmentItemTableOrder($item);
                break;
            case 'support':
                $this->GenerateAssessmentSupportXML($item);
                break;
            case 'related':
                $this->GenerateGroupRelatedXML($item);
                break;
        }

        return $this->assessmentRoot->asXML();
    }

}
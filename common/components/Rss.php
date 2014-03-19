<?php

namespace common\components;

use Yii;
use common\helpers\Url;

class Rss
{
    public $document;
    public $channel;
    public $items;

    public function load($url = false, $unblock = true)
    {
        if($url) {
            $request = Url::get($url, [], 'GET', true);
            $this->loadParser($request['response']);

            return $request['code'];
        }
    }

    public function loadRSS($rawxml = false)
    {
        if($rawxml)
            $this->loadParser($rawxml);
    }

    public function getRSS($includeAttributes = false)
    {
        if($includeAttributes)
            return $this->document;

        return $this->valueReturner();
    }

    public function getChannel($includeAttributes = false)
    {
        if($includeAttributes)
            return $this->channel;

        return $this->valueReturner($this->channel);
    }

    public function getItems($includeAttributes = false)
    {
        if($includeAttributes) {
            return $this->items;
        }
        return $this->valueReturner($this->items);
    }

    private function loadParser($rss = false)
    {
        if($rss) {
            $this->document = array();
            $this->channel = array();
            $this->items = array();
            $DOMDocument = new \DOMDocument;
            $DOMDocument->strictErrorChecking = false;
            $DOMDocument->loadXML($rss);
            $this->document = $this->extractDOM($DOMDocument->childNodes);
        }
    }

    private function valueReturner($valueBlock = false)
    {
        if(!$valueBlock) {
            $valueBlock = $this->document;
        }
        foreach($valueBlock as $valueName => $values) {
            if(isset($values['value'])) {
                $values = $values['value'];
            }
            if(is_array($values) AND count($values)) {
                $valueBlock[$valueName] = $this->valueReturner($values);
            } else {
                $valueBlock[$valueName] = $values;
            }
        }
        return $valueBlock;
    }

    private function extractDOM($nodeList, $parentNodeName = false)
    {
        $itemCounter = 0;
        $tempNode = false;
        foreach($nodeList as $values) {
            if(substr($values->nodeName, 0, 1) != '#') {
                if($values->nodeName == 'item') {
                    $nodeName = $values->nodeName . ':' . $itemCounter;
                    $itemCounter++;
                } else {
                    $nodeName = $values->nodeName;
                }
                $tempNode[$nodeName] = array();
                if($values->attributes) {
                    for($i = 0 ; $values->attributes->item($i) ; $i++) {
                        $tempNode[$nodeName]['properties'][$values->attributes->item($i)->nodeName] = $values->attributes->item($i)->nodeValue;
                    }
                }
                if(!$values->firstChild) {
                    $tempNode[$nodeName]['value'] = $values->textContent;
                } else {
                    $tempNode[$nodeName]['value'] = $this->extractDOM($values->childNodes, $values->nodeName);
                }
                if(in_array($parentNodeName, array('channel', 'rdf:RDF'))) {
                    if($values->nodeName == 'item') {
                        $this->items[] = $tempNode[$nodeName]['value'];
                    } elseif(!in_array($values->nodeName, array('rss', 'channel'))) {
                        $this->channel[$values->nodeName] = $tempNode[$nodeName];
                    }
                }
            } elseif(substr($values->nodeName, 1) == 'text') {
                $tempValue = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", ' ', $values->textContent)));
                if($tempValue) {
                    $tempNode = $tempValue;
                }
            } elseif(substr($values->nodeName, 1) == 'cdata-section') {
                $tempNode = $values->textContent;
            }
        }
        return $tempNode;
    }
}
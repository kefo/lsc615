<?php

/*
 * Record Class
 */

class Record
{

    public $ruri;
	public $rxml;
	public $rtxt;
	
	function Record() {
		
	}
	
	function getRecord($uri) {
	    $this->ruri = $uri;
	    if (strpos($uri, "http://") !== false) {
	        $this->rxml = file_get_contents($uri);
	        return true;
	    }
	    return false;
	}
	
	function recordToTxt() {
		
		$record = $this->recordToArray();
		ksort($record);
		$txt="\n";
		foreach ($record as $k=>$r) {
			if ($k == "000") {
				$txt .= "Leader:   " . $r["data"]. "\n";
			} else if (intval($k) < 10) {
				$txt .= $k . " ";
				$txt .= "      ";
				$txt .= $r["data"] . "\n";
			} else {
				$ind1 = "  ";
				$ind2 = "  ";
				if (trim($r["ind1"]) != "") $ind1 = $r["ind1"];
				if (trim($r["ind2"]) != "") $ind2 = $r["ind2"];
				$txt .= $k . " ";
				$txt .= $ind1 . " ";
				$txt .= $ind2 . " ";
				$txt .= $r["data"] . "\n";
			}
		}
		$this->rtxt = $txt;
		//echo $txt;
		//exit;
		return $txt;
		
	}
	
	function recordToArray() {
	    
		$r = new DOMDocument();
		$r->loadXML($this->rxml);
		
		$xp = new DOMXPath($r);
		$xp->registerNamespace("marcxml", "http://www.loc.gov/MARC21/slim"); 

		$marc = array();
		
		$leader_node = $xp->query('/marcxml:record/marcxml:leader');
		$leader = $leader_node->item(0)->nodeValue;
		$marc[000] = array("data"=>$leader);
		
		$cf_nodes = $xp->query('/marcxml:record/marcxml:controlfield');
		foreach($cf_nodes as $cfn) {
			$tag = $cfn->getAttribute("tag");
			$value = $cfn->nodeValue;
			$marc[$tag] = array("data"=>$value);
		}
		
		$df_nodes = $xp->query('/marcxml:record/marcxml:datafield');
		foreach($df_nodes as $dfn) {
			$tag = $dfn->getAttribute("tag");
			$ind1 = $dfn->getAttribute("ind1");
			$ind2 = $dfn->getAttribute("ind2");
			$sf = "";
			foreach($dfn->childNodes as $cn) {
				if ($cn->nodeName == "marcxml:subfield" || $cn->nodeName == "subfield") {
					$code = $cn->getAttribute("code");
					$sf .= "$" . $code . $cn->nodeValue;
				}
			}
			$marc[$tag] = array();
			$marc[$tag]["ind1"]=$ind1;
			$marc[$tag]["ind2"]=$ind2;
			$marc[$tag]["data"]=$sf;
		}
		
		//echo "<pre>";
		//print_r($marc);
		//echo "</pre>";
		//exit;
		return $marc;

		
	}
	
	
}

?>
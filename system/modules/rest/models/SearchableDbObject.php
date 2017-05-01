<?php
/**
	 * Enhanced version of getObjects allowing more flexibility in where criteria
	 * @param String $class
	 * @param Mixed $where
	 * Where can be a string or an array containing search config
	 * eg /SKIP/10/LIMIT/10/AND/name___like/fred/age___between/40___60
	 * eg /SKIP/10/LIMIT/10/AND/name___like/fred/OR/age__between/0___20/age___greater/80
	 * search config can start with 
	 * 	- SKIP
	 * 	- LIMIT
	 * 	- OR
	 * 	- AND
	 * 	- <operator>
	 * subsequent search config is processed as follows
	 * - SKIP and LIMIT are extracted for query generation
	 * - if AND or OR is found, a query group is created
	 * 		- END closes as sub group
	 * - otherwise config pairs are processed as <field__operator>, <data1__data2> until AND or OR is found or the end of the configuration
	 * 		- if AND or OR is found again, a sub query group is created
	 * @param Boolean $useCache
	 * 
	 * @author Steve Ryan
 */
 
class SearchableDbObject extends DbObject {
	
	function haveValue($value) {
		if (is_string($value) && strlen(trim($value))>0) return true;
		else return false;
	}
	
	function generateSearchQueryRule($ruleConfig) {
		// now we expect pairs
		$filter='';
		$keyParts=explode('___',$ruleConfig[0]);
		if (count($keyParts)==2) {
			if ($keyParts[1]=='equal') {
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]."='".$ruleConfig[1]."'";
			} else if ($keyParts[1]=='not_equal') {
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]."!='".$ruleConfig[1]."'";
			} else if ($keyParts[1]=='in') {
				$valueParts=explode("___",$ruleConfig[1]);
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]." IN ('".implode("','",$ruleConfig[1])."'";
			} else if ($keyParts[1]=='not_in') {
				$valueParts=explode("___",$ruleConfig[1]);
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]." NOT IN ('".implode("','",$ruleConfig[1])."'";
			} else if ($keyParts[1]=='begins_with') {
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]." LIKE '".$ruleConfig[1]."%'";
			} else if ($keyParts[1]=='not_begins_with') {
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]." NOT LIKE '".$ruleConfig[1]."%'";
			} else if ($keyParts[1]=='contains') {
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]." LIKE '%".$ruleConfig[1]."%'";
			} else if ($keyParts[1]=='not_contains') {
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]." NOT LIKE '%".$ruleConfig[1]."%'";
			} else if ($keyParts[1]=='ends_with') {
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]." LIKE '%".$ruleConfig[1]."'";
			} else if ($keyParts[1]=='not_ends_with') {
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]." NOT LIKE '%".$ruleConfig[1]."'";
			} else if ($keyParts[1]=='is_empty') {
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]."=''";
			} else if ($keyParts[1]=='is_not_empty') {
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]."!=''";
			} else if ($keyParts[1]=='is_null') {
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]." IS NULL";
			} else if ($keyParts[1]=='is_not_null') {
				if ($this->haveValue($ruleConfig[1])) $filter= $keyParts[0]." IS NOT NULL ";
			}			 
		}
		return $filter;
	} 
	
	function generateSearchQueryRecursive(&$searchConfig) {
		$rules=[];
		// READ TOKENS
		while (count($searchConfig)>0) {
			if ($searchConfig[0]=="AND" || $searchConfig[0]=="OR") {
				$logic=$searchConfig[0];
				$searchConfig=array_slice($searchConfig,1);
				$children=$this->generateSearchQueryRecursive($searchConfig);
				$rules[]=['rules'=>$children,'logic'=>$logic];
			} else if ($searchConfig[0]=="END") {
				$searchConfig=array_slice($searchConfig,1);
				return $rules;
			} else {
				$ruleParts=array_slice($searchConfig,0,2);
				$searchConfig=array_slice($searchConfig,2);
				if ($ruleParts[0] && count($ruleParts[0])>0) {
					$theRule=$this->generateSearchQueryRule($ruleParts);
					if ($this->haveValue($theRule)) $rules[]=$theRule;
				}
			};
		}
		return $rules;		
	}
	// convert REST url into query object containing nested rules 
	function generateSearchQuery($searchConfig) {
		$queryParts=array('logic'=>'AND','limit' => 10,'skip' => 0);
		if ($searchConfig[0]=="LIMIT") {
			$queryParts['limit']=intval($searchConfig[1]); 
			$searchConfig=array_slice($searchConfig,2);
		}
		if ($searchConfig[0]=="SKIP") {
			$queryParts['skip']=intval($searchConfig[1]);
			$searchConfig=array_slice($searchConfig,2);
		}
		/*if ($searchConfig[0]=="ORDERBY") {
			$queryParts['skip']=intval($searchConfig[1]);
			$searchConfig=array_slice($searchConfig,2);
		}*/
		$queryParts['rules']=$this->generateSearchQueryRecursive($searchConfig);
		return $queryParts;
	}
	// convert nested query object into sql string
	function generateSQLQuery($query) {
		$limit='';
		$skip='';
		$orderby='';
		if ($query['limit']>0) $limit=" LIMIT ".$query['limit'];
		if ($query['skip']>0) $skip=" SKIP ".$query['skip'];
		//if ($query['orderby']>0) $orderby=" SKIP ".$query['orderby'];
		$final= $this->generateSQLQueryRecursive($query,$query['logic']);
		//.$skip.$limit;
		return $final;
	}
	
	function generateSQLQueryRecursive($ruleSet) {
		$where='';
		$whereParts=[];
		if (is_array($ruleSet['rules']) && count($ruleSet['rules'])>0) {
			foreach ($ruleSet['rules'] as $k => $rule) {
				if (is_array($rule) && count($rule)>0) {
					$iRule=$this->generateSQLQueryRecursive($rule);
					if (is_string($iRule) && strlen($iRule)>0) {
						$whereParts[]=$iRule;
					}
				} else if (is_string($rule) && strlen($rule)>0) {
					$whereParts[]=$rule;
				}
			}
			if (count($whereParts)>0) $where='('.implode(' '.$ruleSet['logic'].' ',$whereParts).')';
		}
		return $where;
	} 
	 
	function searchObjects($class,$where=null,$cache_list = false, $use_cache = true) {				
		// hack on where converting it to a string
		if (is_array($where))  {
			if (class_exists($class)) {
				$o = new $class($this->w);
				$q=$this->generateSearchQuery($where);
				$where=$this->generateSQLQuery($q);
				//echo $where;
			}	
			$order_by='';
		}
		return $this->getObjects($class,$where,$cache_list , $use_cache , $order_by );
	}
	
	
}	

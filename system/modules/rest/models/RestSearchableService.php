<?php
/**
	 * Service providing search function where search is configured by an array of parameters that match the REST style URL
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
class RestQueryPart {
	public $filter='';
	public $parameters=[];
}
 
class RestSearchableService extends DbService {
	
	function search($class,$restQuery=[],$allowDeleted=false) {		
		if ($class && count(trim($class))>0 &&class_exists($class)) {			
			$whereString='';
			$skip='';
			$limit='';
			$groupBy='';
			$orderBy='';
			$parameters=[];
			$o=new $class($this->w);
				
			if (is_array($restQuery) &&  count($restQuery)>0)  {
				$parts=$this->extractPartsFromRestQuery($restQuery);
				if (!$allowDeleted) {
					$deletedCondition=array('AND','is_deleted___equal','0');
					$parts['rules']=array_merge($deletedCondition,$parts['rules']);
				} 
				$parts['rules']=$this->generateSearchQueryFromRestUrl($parts['rules']);
				
				$parts['logic']='AND';
				$where=$this->generateSQLWhere($parts);
				if (strlen(trim($where->filter))>0) $whereString='WHERE '.$where->filter;
				$parameters=$where->parameters;
				if ($parts['limit']>0) {
					$limit=" LIMIT ".$parts['limit'];
				}
				if ($parts['skip']>0) {
					$skip=" OFFSET ".$parts['skip'];
				}
				if ($parts['groupBy']>0) {
					$groupBy=" GROUP BY ".$parts['groupBy'];
				}
				if ($parts['orderBy']>0) {
					$orderBy=" ORDER BY ".$parts['orderBy'];
				}
			} else {
				if (!$allowDeleted) {
					$whereString=' WHERE is_deleted=0';
				}
			}
			$objects=[];
			$o=new $class($this->w);
			$columns=$o->getDbTableColumnNames();
			foreach ($columns as $a =>$column) { 
				if (startsWith($column,'dt_') || startsWith($column,'d_') ||startsWith($column,'t_')) {
					$columns[$a]='unix_timestamp('.$column.') '.$column;
				}
			}
			$sql='select '.implode(",",$columns).' from '.$o->getDbTableName().' '.$whereString.$groupBy.$orderBy.$limit.$skip;
			$statement=$this->_db->prepare($sql); 
			$statement->execute($parameters);
			foreach ($statement->fetchAll() as $k =>$rowValue) {
				$o=new $class($this->w);
				$o->fill($rowValue);
				$objects[]=$o;
			}
			return $objects;
		} else {
			return [];
		}
	}
	
	
	// NOTE THAT THE URL _MUST_ PRESENT MODIFIERS IN THE FOLLOWING ORDER SKIP,LIMIT,JOINS,FIELDS,GROUPBY,ORDERBY,AND||OR RULES
	function extractPartsFromRestQuery($query) {
		$skip=0;
		if ($query[0]=="SKIP") {
			$skip=$query[1];
			$query=array_slice($query,2);
		} 
		$limit=10;
		if ($query[0]=="LIMIT") {
			$limit=$query[1];
			$query=array_slice($query,2);
		}
		// JOINS AND FIELDS RESTRICTIONS NOT YET IMPLEMENTED
		$joins=[];
		$fields=[];
		
		$groupBy='';
		if ($query[0]=="GROUPBY") {
			$limit=$query[1];
			$query=array_slice($query,2);
		}
		$orderBy='';
		if ($query[0]=="ORDERBY") {
			$limit=$query[1];
			$query=array_slice($query,2);
		}
		return ['skip'=>$skip,'limit'=>$limit,'groupBy'=>$groupBy,'orderBy'=>$orderBy,'joins'=>$joins,'fields'=>$fields,'rules'=>$query];
	}

	// convert REST url into query object containing nested rules 
	function generateSearchQueryFromRestUrl(&$searchConfig) {
		$rules=[];
		// READ TOKENS
		while (count($searchConfig)>0) {
			if ($searchConfig[0]=="AND" || $searchConfig[0]=="OR") {
				$logic=$searchConfig[0];
				$searchConfig=array_slice($searchConfig,1);
				$children=$this->generateSearchQueryFromRestUrl($searchConfig);
				$rules[]=['rules'=>$children,'logic'=>$logic];
			} else if ($searchConfig[0]=="END") {
				$searchConfig=array_slice($searchConfig,1);
				return $rules;
			} else {
				$ruleParts=array_slice($searchConfig,0,2);
				$searchConfig=array_slice($searchConfig,2);
				if ($ruleParts[0] && count($ruleParts[0])>0) {
					$theRule=$this->generateSearchQueryRule($ruleParts);
					$rules[]=$theRule;
				}
			};
		}
		return $rules;		
	}

	function haveValue($value) {
		if (is_string($value) && strlen(trim($value))>0) return true;
		else return false;
	}
	
	function generateSearchQueryRule($ruleConfig) {
		// now we expect pairs
		$filter='';
		$queryPart=new RestQueryPart();
		$queryPart->filter='';
		$queryPart->parameters=[];
		$keyParts=explode('___',$ruleConfig[0]);
		$valueParts=$ruleConfig[1];
		// query with with timestamps
		if (startsWith($keyParts[0],'dt_') || startsWith($keyParts[0],'d_')) {
			//$valueParts=date("Y-m-d H:i:s",$valueParts);
			$keyParts[0]='unix_timestamp('.$keyParts[0].')';
		}
		
		if (count($keyParts)==2) {
			if ($keyParts[1]=='equal') {
				if ($this->haveValue($valueParts)) {
					$queryPart->filter= $keyParts[0]."=?";
					$queryPart->parameters=array($valueParts);
				}
			} else if ($keyParts[1]=='not_equal') {
				if ($this->haveValue($valueParts)) {
					$queryPart->filter= $keyParts[0]."!=?";
					$queryPart->parameters=array($valueParts);
				}
			} else if ($keyParts[1]=='in') {
				if ($this->haveValue($ruleConfig[1])) {
					$valueParts=explode('___',$ruleConfig[1]);
					$queryPart->filter= 'find_in_set('.$keyParts[0].',?)';
					$queryPart->parameters=array(implode(',',$valueParts));
				}
			} else if ($keyParts[1]=='not_in') {
				if ($this->haveValue($valueParts)) {
					$valueParts=explode('___',$valueParts);
					$queryPart->filter= ' NOT find_in_set('.$keyParts[0].',?)';
					$queryPart->parameters=array(implode(',',$valueParts));
				}
			} else if ($keyParts[1]=='between') {
				$valueParts=explode('___',$ruleConfig[1]);
				if ($this->haveValue($ruleConfig[1]) && count($valueParts)==2) {
					$queryPart->filter= $filter= $keyParts[0]." BETWEEN ? AND ?";
					$queryPart->parameters=array(date("Y-m-d H:i:s",$valueParts[0]),date("Y-m-d H:i:s",$valueParts[1]));
				}
			} else if ($keyParts[1]=='less') {
				if ($this->haveValue($ruleConfig[1])) {
					$queryPart->filter= $filter= $keyParts[0]." < ?";
					$queryPart->parameters=array($valueParts);
				}
			} else if ($keyParts[1]=='less_or_equal') {
				if ($this->haveValue($ruleConfig[1])) {
					$queryPart->filter= $filter= $keyParts[0]." <= ?";
					$queryPart->parameters=array($valueParts);
				}
			} else if ($keyParts[1]=='greater') {
				if ($this->haveValue($ruleConfig[1])) {
					$queryPart->filter= $filter= $keyParts[0]." > ?";
					$queryPart->parameters=array($valueParts);
				}
			} else if ($keyParts[1]=='greater_or_equal') {
				if ($this->haveValue($ruleConfig[1])) {
					$queryPart->filter= $filter= $keyParts[0]." >= ?";
					$queryPart->parameters=array($valueParts);
				}
			} else if ($keyParts[1]=='begins_with') {
				if ($this->haveValue($ruleConfig[1])) {
					$queryPart->filter= $filter= $keyParts[0]." LIKE ?";
					$queryPart->parameters=array($valueParts."%");
				}
			} else if ($keyParts[1]=='not_begins_with') {
				if ($this->haveValue($ruleConfig[1])) {
					$queryPart->filter= $filter= $keyParts[0]." NOT LIKE ?";
					$queryPart->parameters=array($valueParts."%");
				}
			} else if ($keyParts[1]=='contains') {
				if ($this->haveValue($ruleConfig[1])) {
					$queryPart->filter= $filter= $keyParts[0]." LIKE ?";
					$queryPart->parameters=array("%".$valueParts."%");
				}
			} else if ($keyParts[1]=='not_contains') {
				if ($this->haveValue($ruleConfig[1])) {
					$queryPart->filter= $filter= $keyParts[0]." NOT LIKE ?";
					$queryPart->parameters=array("%".$valueParts."%");
				}
			} else if ($keyParts[1]=='ends_with') {
				if ($this->haveValue($ruleConfig[1])) {
					$queryPart->filter= $filter= $keyParts[0]." LIKE ?";
					$queryPart->parameters=array("%".$valueParts);
				}
			} else if ($keyParts[1]=='not_ends_with') {
				if ($this->haveValue($ruleConfig[1])) {
					$queryPart->filter= $filter= $keyParts[0]." NOT LIKE ?";
					$queryPart->parameters=array("%".$valueParts);
				}
			} else if ($keyParts[1]=='is_empty') {
				if ($this->haveValue($ruleConfig[1])) $queryPart->filter= $keyParts[0]."=''";
			} else if ($keyParts[1]=='is_not_empty') {
				if ($this->haveValue($ruleConfig[1])) $queryPart->filter= $keyParts[0]."!=''";
			} else if ($keyParts[1]=='is_null') {
				if ($this->haveValue($ruleConfig[1])) $queryPart->filter= $keyParts[0]." IS NULL";
			} else if ($keyParts[1]=='is_not_null') {
				if ($this->haveValue($ruleConfig[1])) $queryPart->filter= $keyParts[0]." IS NOT NULL ";
			}			 
		}
		return $queryPart;
	} 
	
	function generateSQLWhere($ruleSet) {
		$ret=$this->generateSQLWhereRecursive($ruleSet);
		return $ret;
	}
	function generateSQLWhereRecursive($ruleSet) {
		$where='';
		$parameters=[];
		$whereParts=[];
		if (is_array($ruleSet['rules']) && count($ruleSet['rules'])>0) {
			foreach ($ruleSet['rules'] as $k => $rule) {
				if (is_array($rule) && array_key_exists('rules',$rule)) {
					$iRule=$this->generateSQLWhereRecursive($rule);
					if (get_class($iRule)=="RestQueryPart" && strlen($iRule->filter)>0) {
						$whereParts[]=$iRule->filter;
						$parameters=array_merge($parameters,$iRule->parameters);
					}
				} else if (get_class($rule)=="RestQueryPart" && strlen($rule->filter)>0) {
					$whereParts[]=$rule->filter;
					$parameters=array_merge($parameters,$rule->parameters);
				}
			}
			if (count($whereParts)>0) $where='('.implode(' '.$ruleSet['logic'].' ',$whereParts).')';
		}
		$ret=new RestQueryPart();
		$ret->filter=$where;
		$ret->parameters=$parameters;
		return $ret;
	} 
	 
	
}	

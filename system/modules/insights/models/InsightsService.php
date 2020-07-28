<?php

class InsightsService extends DbService {

}
// returns all insights reports instances
public function GetAllInsights() {
    return $this->GetObjects('Insights',['is_deleted'=>0]);
}

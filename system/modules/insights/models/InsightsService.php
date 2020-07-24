<?php

class InsightsService extends DbService {

}
// returns all insights reports instances
public function GetAllReports() {
    return $this->GetObjects('InsightsReports',['is_deleted'=>0]);
}

<?php
class InsightMembers extends InsightBaseClass
{

  public $name = "Insight Memebers Class";
  Public $description = "Display settings for different membership types";

  public function getFilters(Web $w): array{
    if $memberType = 'admin'{
        return TaskService::getInstance($w)->getAllMembersForInsightClass
    };
    else{
        return TastService::getInstance($w)->getUserMembershipForInsight
    };

  public function run(Web $w, array $params = []): array{
    echo $insight_members;
  }
}

?>
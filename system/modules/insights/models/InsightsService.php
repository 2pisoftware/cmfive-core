<?php

class InsightsService extends Insight
{
    // returns all insights reports instances
    public function GetAllInsights($module)
    {
        $availableInsights = [];

		// Read insights directory for all insights
		if ($module === 'insights') {
			foreach($this->w->modules() as $insight) {
				$availableInsights += $this->getInsightsForModule($insight);
			}
		} else {
			$availableInsights = $this->getInsightsForModule($module);
		}
		
		return $availableInsights;
    }

    public function getInsightsForModule($insight)
    {

    }
}

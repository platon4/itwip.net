<?php

class CMainStat extends CWidget
{
	public function init()
	{
		$command = Yii::app()->db->createCommand();
		
		$all_accounts = $command->select('COUNT(*) as count')->from('it_accounts')->queryRow();
		
		$command->reset();
		
		$all_tw_accounts = $command->select('COUNT(*) as count')->from('it_tw_accounts')->queryRow();	
		
		$this->render('mainStat', 
			array(
                'tw_accounts_count' => $all_tw_accounts['count'],
				'accounts_count' => $all_accounts['count'],

			));
	}	
}
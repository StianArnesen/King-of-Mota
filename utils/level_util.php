<?php

class LevelUtils
{

	public function getExpToNextLevel($CURRENT_LEVEL)
	{
		if($CURRENT_LEVEL < 100)
		{
			$newExp = (100*($CURRENT_LEVEL)) + (100*($CURRENT_LEVEL + 1));	
		}
		else
		{
			$newExp = (100*100);
		}
		
		return $newExp;
	}
}
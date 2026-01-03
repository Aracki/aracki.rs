<?php
	function utils_getNavigationChain($baseLink, $rowCount, $numbOnPage, $current)
	{
		$navigationChainStart = "<div style='padding: 10px 0px 10px 10px; text-align: center;'>";
		$navigationChain = "";
		$navigationChainEnd = "</div>";

		$numbOfPages = ceil($rowCount/$numbOnPage) - 1;
		$needCorrect = false;
		$startOffset = 0;
		$endOffset = $numbOfPages;

		// how many offsets on left and right side of selected
		$sideVisible = 5;
		
		//integer values of given variables
		$rowCount = intval($rowCount);
		$numbOnPage = intval($numbOnPage);
		$current = intval($current);
		
		//findout startOffset and endOffset
		if (($current - $sideVisible) > 0)
		{
			$startOffset = $current - $sideVisible;
		}
		else
		{
			$needCorrect = true;
		}
			
		if ((intval($current) + $sideVisible) <= $endOffset)
		{
			$endOffset = intval($current) + $sideVisible;

			if ($needCorrect)
			{
				$endOffset += ($sideVisible - $current);

				if ($endOffset > $numbOfPages)
				{
					$endOffset = $numbOfPages;
				}
			}
		}
		else
		{
			$startOffset -= ($current+$sideVisible) - $endOffset;

			if ($startOffset < 0) $startOffset = 0;
		}
		
		// now build navigation chain
		for ($i = $startOffset; $i <= $endOffset; $i++)
		{
			if ($i != $current)
			{
				$navigationChain .="<a href='".createOffsetLink($baseLink, $i)."'>".($i+1)."</a> ";
			}
			else
			{
				$navigationChain .= "<b>".($i+1)."</b> ";
			}
		}

		$fw = "<img src='/images/basic/fw.gif' alt='Next page' border='0' style='display: inline;'/>";
		$ffw = "<img src='/images/basic/ffw.gif' alt='Page #' border='0' style='display: inline;'/>";
		$rw = "<img src='/images/basic/rw.gif' alt='Previous page' border='0' style='display: inline;'/>";
		$rrw = "<img src='/images/basic/rrw.gif' alt='Page #' border='0' style='display: inline;'/>";
		
		if (strpos($navigationChain, "a href") == false)
		{
			$navigationChain = "";
		}
		else
		{
			$left = "";
			$right = "";

			if ($current < $numbOfPages)
			{
				$right = "<a href='".createOffsetLink($baseLink, $current+1)."'>".$fw."</a>";

				if ($endOffset < $numbOfPages)
				{
					$ffw = ereg_replace("[#]", "".($endOffset+2), $ffw);
					$right .= " <a href='".createOffsetLink($baseLink, $endOffset+1)."'>".$ffw."</a>";
				}
			}

			if ($current > 0)
			{
				$left = "<a href='".createOffsetLink($baseLink, $current-1)."'>".$rw."</a>";

				if ($startOffset > 0)
				{
					$rrw = ereg_replace("[#]", "".($startOffset), $rrw);
					$left = " <a href='".createOffsetLink($baseLink, $startOffset-1)."'>".$rrw."</a> ".$left;
				}
			}
			
			if ($startOffset > 0)
			{
				$navigationChain = "<a href='".createOffsetLink($baseLink, 0)."'>1</a> ... ".$navigationChain;
			}
		
			if ($endOffset < $numbOfPages)
			{
				$navigationChain .= " ... <a href='".createOffsetLink($baseLink, $numbOfPages)."'>".($numbOfPages+1)."</a>";
			}

			$navigationChain = $navigationChainStart.$left." ".$navigationChain." ".$right.$navigationChainEnd;
		}
		
		return $navigationChain;
	}

	function createOffsetLink($baseLink, $offset){
		$baseLink = preg_replace("/&offset=[0-9]+/", "", $baseLink);
		
		if (is_numeric(strpos($baseLink, "javascript:")) || is_numeric(strpos($baseLink, "onclick="))){
			return $baseLink.$offset."); return false;";
		}

		return $baseLink."&offset=".$offset;
	}
?>
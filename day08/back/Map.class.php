<?php

class Map 
{
	static function default_map()
	{
		$map = [];
		foreach (range(0, 99, 1) as $h)
			foreach (range(0, 149, 1) as $w)
				$map[$h][$w] = 0;

		foreach (range(0, 25, 1) as $o)
		{
			$height = rand(1, 15);
			$width = rand(1, 15);

			$x = rand(20 + $height, 130 - $height);
			$y = rand(20 + $width, 80 - $width);
			for ($i=0; $i < $height * $width * 0.2; $i++)
			{
				$map[$y][$x] = 1;
				if ($x < $width - 1 && $map[$y][$x + 1] == 0 && (mt_rand() / mt_getrandmax()) >= 0.5)
					$x++;
				else if ($y < $height - 1 && $map[$y + 1][$x] == 0 && (mt_rand() / mt_getrandmax()) >= 0.5)
					$y++;
				else if ($x > 0 && $map[$y][$x - 1] == 0 && (mt_rand() / mt_getrandmax()) >= 0.5)
					$x--;
				else if ($y > 0 && $map[$y - 1][$x] == 0 && (mt_rand() / mt_getrandmax()) >= 0.5)
					$y--;
			}
		}
		return $map;
	}
}

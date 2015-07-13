<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarOther
	{
		public static $arMessage = array ();

		/**
		 * Функция подготавливает данные прочего расхода для добавления
		 *
		 * @param array $post
		 * @return bool
		 */
		public function AddOtherCosts ($post=array()) {
			if (empty($post)) return false;
			$arData = array();

			$arData["auto"] = intval($post["auto"]);
			$arData["date"] = CInvestToCarMain::ConvertDateToTimestamp($post["date"]);
			$arData["cost"] = floatval(str_replace(",",".",$post["cost"]));
			$arData["type"] = intval($post["type"]);
			$arData["name"] = htmlspecialchars($post["name"]);
			$arData["number"] = floatval(str_replace(",",".",$post["number"]));
			$arData["odo"] = floatval(str_replace(",",".",$post["odo"]));
			$arData["catalog_number"] = htmlspecialchars($post["catalog_number"]);
			$arData["waypoint"] = intval($post["waypoint"]);
			if ($arData["waypoint"]==0) {
				$arData["waypoint"] = CInvestToCarPoints::CreateNewPoint (
					$post["newpoint_name"],
					$post["newpoint_address"],
					$post["newpoint_lon"],
					$post["newpoint_lat"],
					$post["newpoint_type"]
				);
			}
			$arData["comment"] = htmlspecialchars($post["comment"]);

			if ($res = self::AddOtherCostsDB($arData)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция добавляет прочий расход в DB
		 *
		 * @param array $arData
		 * @return bool
		 */
		private function AddOtherCostsDB ($arData=array()) {
			global $DB;
			if (empty($arData)) return false;

			$query = "INSERT INTO `".CInvestToCarMain::GetTableByCode("other")."` (";
			$query .= "`car` , `date` , `cost` , `type` , ";
			$query .= "`name` , `number` , `odo` , `catalog_number` , ";
			$query .= "`waypoint` , `comment`) VALUES (";
			$query .= "'".$arData["auto"]."', '".$arData["date"]."', '".$arData["cost"]."', '".$arData["type"]."', ";
			$query .= "'".$arData["name"]."', '".$arData["number"]."', '".$arData["odo"]."', '".$arData["catalog_number"]."', ";
			$query .= "'".$arData["waypoint"]."', '".$arData["comment"]."');";
			if ($res = $DB->Insert($query)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция возвращает сумму прочих расходов
		 *
		 * @param int $car
		 * @return int
		 */
		public function GetTotalOtherCosts ($car =0) {
			global $DB;
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();

			$query = "SELECT SUM(`cost`) AS `otherCosts` FROM `".CInvestToCarMain::GetTableByCode("other")."` WHERE `car`=".$car;
			if ($res = $DB->Select($query)) {
				return $res[0]["otherCosts"];
			}
			else {
				return 0;
			}
		}

	}
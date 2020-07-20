<?php

namespace Dalee\PPTwig;

/**
 * Class Filters.
 *
 * @package Dalee\PPTwig
 */
class Filters
{

	/**
	 * @param string $string
	 * @return false|int
	 */
	public static function dateToTime(string $string) {
		if ($string == 'today') {
			return mktime(0,0,0);

		} elseif ($string == 'month') {
			return mktime(0,0,0,date('n'),1);

		} elseif ($string != '') {
			preg_match("/^(\d{2})\.(\d{2})\.(\d{4})\s+(\d{2}):(\d{2}):(\d{2})$/si".REGEX_MOD, trim($string), $date);

			return mktime($date[4], $date[5], $date[6], $date[2], $date[1], $date[3]);

		} else {
			return time();
		}
	}

	/**
	 * @param $string
	 * @param string $format
	 * @param null $default_date
	 * @param null $default_strftime
	 * @return string
	 */
	public static function dateFormat($string, $format = "%b %e, %Y", $default_date = null, $default_strftime = null)
	{
		$weekdays = array(
			1 => 'Понедельник',
			2 => 'Вторник',
			3 => 'Среда',
			4 => 'Четверг',
			5 => 'Пятница',
			6 => 'Суббота',
			0 => 'Воскресенье',
		);

		$weekdaysShort = array(
			1 => 'Пн',
			2 => 'Вт',
			3 => 'Ср',
			4 => 'Чт',
			5 => 'Пт',
			6 => 'Сб',
			0 => 'Вс',
		);

		$months = array(
			'01' => 'Январь',
			'02' => 'Февраль',
			'03' => 'Март',
			'04' => 'Апрель',
			'05' => 'Май',
			'06' => 'Июнь',
			'07' => 'Июль',
			'08' => 'Август',
			'09' => 'Сентябрь',
			'10' => 'Октябрь',
			'11' => 'Ноябрь',
			'12' => 'Декабрь',
		);

		$monthsE = array(
			'01' => 'January',
			'02' => 'February',
			'03' => 'March',
			'04' => 'April',
			'05' => 'May',
			'06' => 'June',
			'07' => 'July',
			'08' => 'August',
			'09' => 'September',
			'10' => 'October',
			'11' => 'November',
			'12' => 'December',
		);

		$months2 = array(
			'01' => 'января',
			'02' => 'февраля',
			'03' => 'марта',
			'04' => 'апреля',
			'05' => 'мая',
			'06' => 'июня',
			'07' => 'июля',
			'08' => 'августа',
			'09' => 'сентября',
			'10' => 'октября',
			'11' => 'ноября',
			'12' => 'декабря',
		);

		$months3 = array(
			'01' => 'январе',
			'02' => 'феврале',
			'03' => 'марте',
			'04' => 'апреле',
			'05' => 'мае',
			'06' => 'июне',
			'07' => 'июле',
			'08' => 'августе',
			'09' => 'сентябре',
			'10' => 'октябре',
			'11' => 'ноябре',
			'12' => 'декабре',
		);

		$monthsShort = array(
			'01' => 'Янв',
			'02' => 'Фев',
			'03' => 'Мрт',
			'04' => 'Апр',
			'05' => 'Май',
			'06' => 'Июн',
			'07' => 'Июл',
			'08' => 'Авг',
			'09' => 'Сен',
			'10' => 'Окт',
			'11' => 'Ноя',
			'12' => 'Дек',
		);


		$time = strlen(trim($string)) ? $string : (isset($default_date) ? $default_date : '');
		if (!is_numeric($time)) $time = static::makeTimestamp($time);

		if (!$default_strftime) {
			$format = str_replace('%a', $weekdaysShort[strftime('%w', $time)], $format);
			$format = str_replace('%A', $weekdays[strftime('%w', $time)], $format);

			$format = str_replace('%b', $monthsShort[strftime('%m', $time)], $format);
			$format = str_replace('%B3', $months3[strftime('%m', $time)], $format);
			$format = str_replace('%B2', $months2[strftime('%m', $time)], $format);
			$format = str_replace('%BE', $monthsE[strftime('%m', $time)], $format);
			$format = str_replace('%B', $months[strftime('%m', $time)], $format);
			$format = str_replace('%e', strftime('%d', $time) > 9 ? strftime('%d', $time) : substr(strftime('%d', $time), 1), $format);
			$format = str_replace('%h', $monthsShort[strftime('%m', $time)], $format);
		}

		if ($string != '') {
			return strftime($format, static::makeTimestamp($string));

		} elseif (isset($default_date) && $default_date != '') {
			return strftime($format, static::makeTimestamp($default_date));

		}
	}

	/**
	 * @param string $assetPath
	 * @return string
	 */
	public static function assetUpdated(string $assetPath): string
	{
		$path = sprintf('%shtdocs%s', LOCALPATH, preg_replace('/\?.*/', '', $assetPath));

		if (!file_exists($path)) {
			return $assetPath;
		}

		return sprintf('%s%s_=%s', $assetPath, strpos($assetPath, '?') === false ? '?' : '&', filemtime($path));
	}

	/**
	 * @param $string
	 * @return false|int
	 */
	public static function makeTimestamp($string)
	{
		if(empty($string)) {
			$time = time();
		} elseif (preg_match('/^\d{14}$/', $string)) {
			// it is mysql timestamp format of YYYYMMDDHHMMSS?
			$time = mktime(substr($string, 8, 2),substr($string, 10, 2),substr($string, 12, 2),
				substr($string, 4, 2),substr($string, 6, 2),substr($string, 0, 4));
		} elseif (is_numeric($string)) {
			$time = (int)$string;
		} else {
			$time = strtotime($string);

			if ($time == -1 || $time === false) {
				$time = time();
			}
		}

		return $time;
	}
}

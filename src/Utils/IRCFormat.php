<?php
namespace Utils;

abstract class IRCFormat {

	public static $FORMAT_UNDERLINE = "\037";
	public static $FORMAT_BOLD = "\02";
	public static $FORMAT_REVERSE = "\026";

	public static $FORMAT_ACTION = "\01";

	public static $FORMAT_RESET = "\017";

	public static $COLOR_WHITE = "\00300";
	public static $COLOR_BLACK = "\00301";
	public static $COLOR_BLUE = "\00302";
	public static $COLOR_GREEN = "\00303";
	public static $COLOR_RED = "\00304";
	public static $COLOR_BROWN = "\00305";
	public static $COLOR_PURPLE = "\00306";
	public static $COLOR_ORANGE = "\00307";
	public static $COLOR_YELLOW = "\00308";
	public static $COLOR_LIGHT_GREEN = "\00309";
	public static $COLOR_DARK_AQUA = "\00310";
	public static $COLOR_AQUA = "\00311";
	public static $COLOR_LIGHT_BLUE = "\00312";
	public static $COLOR_PINK = "\00313";
	public static $COLOR_GRAY = "\00314";
	public static $COLOR_LIGHT_GRAY = "\0031";
}
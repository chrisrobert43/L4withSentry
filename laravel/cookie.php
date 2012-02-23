<?php namespace Laravel; defined('DS') or die('No direct script access.');

use Closure;

class Cookie {

	/**
	 * The cookies that have been set.
	 *
	 * @var array
	 */
	public static $jar = array();

	/**
	 * Determine if a cookie exists.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public static function has($name)
	{
		return ! is_null(static::get($name));
	}

	/**
	 * Send all of the cookies to the browser.
	 *
	 * @return void
	 */
	public static function send()
	{
		if (headers_sent()) return false;

		// All cookies are stored in the "jar" when set and not sent directly to the
		// browser. This simply makes testing all of the cookie stuff very easy
		// since the jar can be inspected by the application's tests.
		foreach (static::$jar as $cookie)
		{
			static::set($cookie);
		}
	}

	/**
	 * Send a cookie from the cookie jar back to the browser.
	 *
	 * @param  array  $cookie
	 * @return void
	 */
	protected static function set($cookie)
	{
		extract($cookie);

		$time = ($minutes !== 0) ? time() + ($minutes * 60) : 0;

		// A cookie payload can't exceed 4096 bytes, so if the payload is greater
		// than that, we'll raise an error to warn the developer since it could
		// cause serious cookie-based session problems.
		$value = static::sign($name, $value);

		if (strlen($value) > 4000)
		{
			throw new \Exception("Payload too large for cookie.");
		}

		setcookie($name, $value, $time, $path, $domain, $secure);
	}

	/**
	 * Get the value of a cookie.
	 *
	 * <code>
	 *		// Get the value of the "favorite" cookie
	 *		$favorite = Cookie::get('favorite');
	 *
	 *		// Get the value of a cookie or return a default value 
	 *		$favorite = Cookie::get('framework', 'Laravel');
	 * </code>
	 *
	 * @param  string  $name
	 * @param  mixed   $default
	 * @return string
	 */
	public static function get($name, $default = null)
	{
		if (isset(static::$jar[$name])) return static::$jar[$name]['value'];

		$value = array_get($_COOKIE, $name);

		if ( ! is_null($value) and isset($value[40]) and $value[40] == '~')
		{
			// The hash signature and the cookie value are separated by a tilde
			// character for convenience. To separate the hash and the contents
			// we can simply expode on that character.
			//
			// By re-feeding the cookie value into the "sign" method we should
			// be able to generate a hash that matches the one taken from the
			// cookie. If they don't, the cookie value has been changed.
			list($hash, $value) = explode('~', $value, 2);

			if (static::hash($name, $value) === $hash)
			{
				return $value;
			}
		}

		return value($default);
	}

	/**
	 * Set the value of a cookie.
	 *
	 * <code>
	 *		// Set the value of the "favorite" cookie
	 *		Cookie::put('favorite', 'Laravel');
	 *
	 *		// Set the value of the "favorite" cookie for twenty minutes
	 *		Cookie::put('favorite', 'Laravel', 20);
	 * </code>
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  int     $minutes
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @return void
	 */
	public static function put($name, $value, $minutes = 0, $path = '/', $domain = null, $secure = false)
	{
		static::$jar[$name] = compact('name', 'value', 'minutes', 'path', 'domain', 'secure');
	}

	/**
	 * Set a "permanent" cookie. The cookie will last for one year.
	 *
	 * <code>
	 *		// Set a cookie that should last one year
	 *		Cookie::forever('favorite', 'Blue');
	 * </code>
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @return bool
	 */
	public static function forever($name, $value, $path = '/', $domain = null, $secure = false)
	{
		return static::put($name, $value, 525600, $path, $domain, $secure);
	}

	/**
	 * Generate a cookie signature based on the contents.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @return string
	 */
	public static function sign($name, $value)
	{
		return static::hash($name, $value).'~'.$value;
	}

	/**
	 * Generate a cookie hash based on the contents.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @return string
	 */
	protected static function hash($name, $value)
	{
		return sha1($name.$value.Config::get('application.key'));
	}

	/**
	 * Delete a cookie.
	 *
	 * @param  string  $name
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @return bool
	 */
	public static function forget($name, $path = '/', $domain = null, $secure = false)
	{
		return static::put($name, null, -2000, $path, $domain, $secure);
	}

}
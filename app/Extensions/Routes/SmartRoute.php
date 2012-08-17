<?php

namespace Extensions\Routes;
use Nette
,	Nette\Application\Request
,   Nette\Application\Routers\Route
,	Nette\Application\IRouter
,   Nette\Http\IRequest
,   Nette\Http\Url
,   Nette\InvalidStateException;
;



class SmartRoute extends Route implements IRouter
{
	const WAY_IN = 'in';
	const WAY_OUT = 'out';

	const ALIAS = 'alias';

	/** @var array */
	private $filters = array();


	/**
	 * @param string $mask
	 * @param array $metadata
	 * @param int $flags
	 */
	public function __construct($mask, $metadata = array(), $flags = 0)
	{

		parent::__construct($mask, $metadata, $flags);

		foreach ($metadata as $part => &$value)
		{
			if (is_array($value) && in_array(self::ALIAS, $value))
			{
				$this->addFilter($part,
					'Extensions\Routes\UrlResolve::urlToLink',
					'Extensions\Routes\UrlResolve::linkToUrl'
				);
			}
		}
	}

	/**
	 * @param \Nette\Http\IRequest $httpRequest
	 * @return Request|NULL
	 * Maps HTTP request to a Request object.
	 */
	public function match(IRequest $httpRequest)  {
		$appRequest = parent::match($httpRequest);
		if (!$appRequest) {
			return $appRequest;
		}

		if ($params = $this->doFilterParams($this->getRequestParams($appRequest), $appRequest, self::WAY_IN)) {
			return $this->setRequestParams($appRequest, $params);
		}

		return NULL;
	}


	/**
	 * Constructs absolute URL from Request object.
	 * @param \Nette\Application\Request $appRequest
	 * @param \Nette\Http\Url $refUrl
	 * @return NULL|string
	 */
	public function constructUrl(Request $appRequest, Nette\Http\Url $refUrl) {
		if ($params = $this->doFilterParams($this->getRequestParams($appRequest), $appRequest, self::WAY_OUT)) {
			$appRequest = $this->setRequestParams($appRequest, $params);
			return parent::constructUrl($appRequest, $refUrl);
		}

		return NULL;
	}



	/**
	 * @param string $param
	 * @param callable $in
	 * @param callable $out
	 * @return SmartRoute
	 */
	public function addFilter($param, $in, $out = NULL)
	{
		$this->filters[$param] = array(
				self::WAY_IN => callback($in),
				self::WAY_OUT => $out ? callback($out) : NULL
			);

		return $this;
	}



	/**
	 * @return array
	 */
	public function getFilters()
	{
		return $this->filters;
	}


	/**
	 * @param \Nette\Application\Request $appRequest
	 * @return array
	 */
	private function getRequestParams(Request $appRequest)
	{
		$params = $appRequest->getParameters();
		$metadata = $this->getDefaults();

		$presenter = $appRequest->getPresenterName();
		$params[self::PRESENTER_KEY] = $presenter;

		if (isset($metadata[self::MODULE_KEY])) { // try split into module and [submodule:]presenter parts
			$module = $metadata[self::MODULE_KEY];
			if (isset($module['fixity']) && strncasecmp($presenter, $module[self::VALUE] . ':', strlen($module[self::VALUE]) + 1) === 0) {
				$a = strlen($module[self::VALUE]);
			} else {
				$a = strrpos($presenter, ':');
			}

			if ($a === FALSE) {
				$params[self::MODULE_KEY] = '';
			} else {
				$params[self::MODULE_KEY] = substr($presenter, 0, $a);
				$params[self::PRESENTER_KEY] = substr($presenter, $a + 1);
			}
		}

		return $params;
	}


	/**
	 * @param \Nette\Application\Request $appRequest
	 * @param array $params
	 * @return \Nette\Application\Request
	 * @throws InvalidStateException
	 */
	private function setRequestParams(Request $appRequest, array $params)
	{
		$metadata = $this->getDefaults();

		if (!isset($params[self::PRESENTER_KEY])) {
			throw new InvalidStateException('Missing presenter in route definition.');
		}
		if (isset($metadata[self::MODULE_KEY])) {
			if (!isset($params[self::MODULE_KEY])) {
				throw new InvalidStateException('Missing module in route definition.');
			}
			$presenter = $params[self::MODULE_KEY] . ':' . $params[self::PRESENTER_KEY];
			unset($params[self::MODULE_KEY], $params[self::PRESENTER_KEY]);

		} else {
			$presenter = $params[self::PRESENTER_KEY];
			unset($params[self::PRESENTER_KEY]);
		}

		$appRequest->setPresenterName($presenter);
		$appRequest->setParameters($params);

		return $appRequest;
	}


	/**
	 * @param array|\stdClass $params
	 * @param \Nette\Application\Request $request
	 * @param string $way
	 * @return array|null
	 */
	private function doFilterParams($params, Request $request, $way)
	{
		// tady mám k dispozici všechny parametry
		foreach ($this->getFilters() as $param => $filters) {
			if (!isset($params[$param]) || !isset($filters[$way])) {
				continue; // param not found
			}

			$params[$param] = call_user_func($filters[$way], (string) $params[$param], $request);
			if ($params[$param] === NULL) {
				return NULL; // rejected by filter
			}
		}

		return $params;
	}

}
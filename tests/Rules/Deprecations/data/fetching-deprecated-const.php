<?php

namespace FetchingDeprecatedConst {

	\FILTER_FLAG_SCHEME_REQUIRED;
	\FILTER_FLAG_HOST_REQUIRED;
	FILTER_FLAG_SCHEME_REQUIRED;
	FILTER_FLAG_HOST_REQUIRED;

	/**
	 * @deprecated
	 */
	function deprecated_scope()
	{
		\FILTER_FLAG_SCHEME_REQUIRED;
		\FILTER_FLAG_HOST_REQUIRED;
	}

	/**
	 * @deprecated nothing to see
	 */
	class deprecated_scope2
	{

		public function test()
		{
			\FILTER_FLAG_SCHEME_REQUIRED;
			\FILTER_FLAG_HOST_REQUIRED;
		}

	}

}

namespace FetchingDeprecatedConst\Redefined {

	\FILTER_FLAG_SCHEME_REQUIRED;
	\FILTER_FLAG_HOST_REQUIRED;
	FILTER_FLAG_SCHEME_REQUIRED; // ok
	FILTER_FLAG_HOST_REQUIRED; // ok

}

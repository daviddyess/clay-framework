<?php
/**
 * @file index.php
 * @brief Entry point for Clay Application Platforms
 * @details The index.php (or other entry point) uses \Clay::Bootstrap() via \Clay() to determine which configuration is to be used
 */
/*
 * Clay Framework
 *
 * @copyright (C) 2007-2012 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

# Add the base directory of Clay to the include path
# If you use any entry point in a different folder, you will need to adjust the Path here
set_include_path(dirname(dirname(__FILE__)) . PATH_SEPARATOR . get_include_path());
	
# Include the Clay Library
include 'library/Clay.php';

# Run Clay
\Clay('default');
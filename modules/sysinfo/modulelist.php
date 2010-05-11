<?php
/**
 * List all existing modules (optionally, in a given extension)
 * @author G. Giunta
 * @version $Id: cachestats.php 18 2010-04-17 14:29:21Z gg $
 * @copyright (C) G. Giunta 2010
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 */

// rely on system policy instead of creating our own, but allow also PolicyOmitList
$ini = eZINI::instance();
if ( !in_array( 'sysinfo/modulelist', $ini->variable( 'RoleSettings', 'PolicyOmitList' ) ) )
{
    $user = eZUser::currentUser();
    $access = $user->hasAccessTo( 'setup', 'system_info' );
    if ( $access['accessWord'] != 'yes' )
    {
        $module = $Params['Module'];
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
    }
}

// generic info for all modules: number of views, fetch functions, policy functions, name of extension
$moduleList = array();

/// @todo spit an error msg if given extenion name is not an active ext

$modules = eZModuleLister::getModuleList();
foreach( $modules as $modulename => $path )
{
    $module = eZModule::exists( $modulename );
    if ( $module instanceof eZModule )
    {
        $extension = '';
        if ( preg_match( '#extension/([^/]+)/modules/#', $path, $matches ) )
        {
            $extension = $matches[1];
        }
        if ( $Params['extensionname'] == '' || $Params['extensionname'] == $extension )
        {
            /// @todo this generates a warning in debug logs if there are no functions; how to avoid it?
            ///       The only way seems to pre_test for file existence, looking only
            ///       at the same dir as where the module is
            $functions = eZFunctionHandler::moduleFunctionInfo( $modulename );
            $moduleList[$modulename] = array(
                'views' => count( $module->attribute( 'views' ) ),
                'fetch_functions' => count( $functions->FunctionList ),
                'policy_functions' => count( $module->attribute( 'available_functions' ) ),
                'extension' => $extension
            );
        }
    }
}
ksort( $moduleList );

$title = 'List of available modules';
if ( $Params['extensionname'] != '' )
{
    $title .= ' in extension "' . $Params['extensionname'] . '"';
}

require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( 'title', $title );
$tpl->setVariable( 'modulelist', $moduleList );

$Result = array();
$Result['content'] = $tpl->fetch( "design:sysinfo/modulelist.tpl" ); //var_dump($cacheFilesList);

$Result['left_menu'] = 'design:parts/sysinfo/menu.tpl';
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'SysInfo', 'Modules' ) ) );
if ( $Params['extensionname'] != '' )
{
    $Result['path'][0]['url'] = '/sysinfo/viewlist';
    $Result['path'][] = array( 'url' => false,
                               'text' => $Params['extensionname'] );
}
?>
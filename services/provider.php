<?php
/* ------------------------------------------------------------------------
 * lightrss - Light RSS for Joomla 4.x/5.x from Prieco Ligth RSS
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (C) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
*/
defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The quickicon module service provider.
 *
 * @since  4.0.0
 */
return new class implements ServiceProviderInterface
{
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function register(Container $container)
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\ConseilGouz\\Module\\LightRSS'));
        $container->registerServiceProvider(new HelperFactory('\\ConseilGouz\\Module\\LightRSS\\Site\\Helper'));

        $container->registerServiceProvider(new Module());
    }
};

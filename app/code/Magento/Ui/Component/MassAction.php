<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Component;

/**
 * Mass action UI component.
 *
 * @api
 * @since 100.0.2
 */
class MassAction extends AbstractComponent
{
    const NAME = 'massaction';

    /**
     * @inheritDoc
     */
    public function prepare()
    {
        $config = $this->getConfiguration();
        $childComponents = $this->getChildComponents();
        
        // Merge actions defined in config with child actions
        // This allows for disabling via meta data rather than just in uicomponent XML
        if (isset($config['actions'])) {
            foreach ($config['actions'] as $actionKey => $action) {
                if (!isset($action['type'])) {
                    continue;
                }
                foreach ($childComponents as $childComponent) {
                    $childConfig = $childComponent->getConfiguration();
                    if (isset($childConfig['type']) && $childConfig['type'] === $action['type']) {
                        $childComponent->setData(
                            'config',
                            array_replace_recursive($childConfig, $action)
                        );
                        unset($config['actions'][$actionKey]);
                        break;
                    }
                }
            }    
        }

        foreach ($childComponents as $actionComponent) {
            $componentConfig = $actionComponent->getConfiguration();
            $disabledAction = $componentConfig['actionDisable'] ?? false;
            if ($disabledAction) {
                continue;
            }
            $config['actions'][] = $componentConfig;
        }

        $origConfig = $this->getConfiguration();
        if ($origConfig !== $config) {
            $config = array_replace_recursive($config, $origConfig);
        }

        $this->setData('config', $config);
        $this->components = [];

        parent::prepare();
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }
}

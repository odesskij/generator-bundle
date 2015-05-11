<?php
namespace Odesskij\Bundle\GeneratorBundle\Service;

use Odesskij\Bundle\GeneratorBundle\Exception\ValidateException;

/**
 * Validator.
 *
 * @author Vladimir Odesskij <odesskij1992@gmail.com>
 */
class Validator
{
    /**
     * @param string $namespace
     * @throws ValidateException
     * @return string
     */
    public function validateBundleNamespace($namespace)
    {
        return $namespace;
    }

    /**
     * @param string $bundle
     * @throws ValidateException
     * @return string
     */
    public function validateBundleName($bundle)
    {
        return $bundle;
    }
}
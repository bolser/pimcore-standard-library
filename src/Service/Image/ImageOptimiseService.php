<?php

/**
 * ImageOptimiseService
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md
 * file distributed with this source code.
 *
 * @copyright  Copyright (c) 2001-2017 Bolser Digital Agency (https://www.bolser.co.uk)
 * @license    GNU General Public License version 3 (GPLv3)
 */

namespace Bolser\Pimcore\Service\Image;

use ImageOptimizer\OptimizerFactory;

/**
 * Class ImageOptimiseService
 *
 * @package Bolser\Pimcore\Service\Image
 */
class ImageOptimiseService
{
    /**
     * Optimises an image
     *
     * @param string $imagePath
     * @param array $options (Optional) Options which are detailed above.
     *                       Some necessary defaults are set if no options are provided
     * @return bool
     *
     * ignore_errors (default: true)
     * optipng_options (default: array('-i0', '-o2', '-quiet')) - an array of arguments to pass to the library
     * pngquant_options (default: array('--force'))
     * pngcrush_options (default: array('-reduce', '-q', '-ow'))
     * pngout_options (default: array('-s3', '-q', '-y'))
     * gifsicle_options (default: array('-b', '-O5'))
     * jpegoptim_options (default: array('--strip-all', '--all-progressive'))
     * jpegtran_options (default: array('-optimize', '-progressive'))
     * optipng_bin (default: will be guessed) - you can enforce paths to binaries, but by default it will be guessed
     * pngquant_bin
     * pngcrush_bin
     * pngout_bin
     * gifsicle_bin
     * jpegoptim_bin
     * jpegtran_bin
     *
     */
    public function optimise($imagePath, array $options = [])
    {
        $imageMimeType = mime_content_type($imagePath);

        $options = $this->setupOptions($options);

        $factory = new OptimizerFactory($options);

        switch ($imageMimeType) {
            case 'image/png':
                $optimizer = $factory->get('pngquant');
                break;
            case 'image/jpeg':
                $optimizer = $factory->get('jpegoptim');
                break;
            case 'image/gif':
                $optimizer = $factory->get('gif');
                break;
            default:
                return false;
        }

        $optimizer->optimize($imagePath);

        return true;
    }

    /**
     * Checks the options array and adds any missing options that we need to optimise images
     *
     * This allows for us to manually override any options as well as set some we may need when reusing this class
     *
     * @param array $options
     *
     * @return array
     */
    private function setupOptions(array $options)
    {
        $binFolder = '/usr/bin/';
        if (!array_key_exists('optipng_bin', $options)) {
            $options['optipng_bin'] = $binFolder . 'optipng';
        }
        if (!array_key_exists('pngquant_bin', $options)) {
            $options['pngquant_bin'] = $binFolder . 'pngquant';
        }
        if (!array_key_exists('pngcrush_bin', $options)) {
            $options['pngcrush_bin'] = $binFolder . 'pngcrush';
        }
        if (!array_key_exists('jpegoptim_bin', $options)) {
            $options['jpegoptim_bin'] = $binFolder . 'jpegoptim';
        }
        if (!array_key_exists('jpegtran_bin', $options)) {
            $options['jpegtran_bin'] = $binFolder . 'jpegtran';
        }
        if (!array_key_exists('gifsicle_bin', $options)) {
            $options['gifsicle_bin'] = $binFolder . 'gifsicle';
        }
        if (!array_key_exists('ignore_errors', $options)) {
            $options['ignore_errors'] = false;
        }
        
        return $options;
    }
}
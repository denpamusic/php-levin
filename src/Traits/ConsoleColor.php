<?php

namespace Denpa\Levin\Traits;

use InvalidArgumentException;

trait ConsoleColor
{
    /**
     * @var string
     */
    protected $color;

    /**
     * @var string
     */
    protected $background;

    /**
     * @var int
     */
    protected $foregroundBase = 30;

    /**
     * @var int
     */
    protected $backgroundBase = 40;

    /**
     * @var int
     */
    protected $reset = 0;

    /**
     * @var array
     */
    protected $colors = [
        'black'   => 0,
        'red'     => 1,
        'green'   => 2,
        'yellow'  => 3,
        'blue'    => 4,
        'magenta' => 5,
        'cyan'    => 6,
        'white'   => 7,
    ];

    /**
     * @var array
     */
    protected $modifiers = [
        'regular' => 0,
        'bright'  => 1,
    ];

    /**
     * @var bool
     */
    protected $colorDisabled = false;

    /**
     * @param string $color
     *
     * @return self
     */
    public function color(string $color) : self
    {
        list($color, $modifier) = $this->parseColorString($color);

        $this->color = sprintf(
            '%d;%d',
            $this->modifiers[$modifier],
            $this->foregroundBase + $this->colors[$color]
        );

        return $this;
    }

    /**
     * @param string $background
     *
     * @return self
     */
    public function background(string $background) : self
    {
        list($color, $modifier) = $this->parseColorString($background);

        $this->background = $this->backgroundBase + $this->colors[$color];

        return $this;
    }

    /**
     * @param string $message
     * @param mixed  $args,...
     *
     * @return self
     */
    public function error(string $message, ...$args) : self
    {
        return $this
            ->resetColors()
            ->color('white')
            ->background('red')
            ->line($message, ...$args)
            ->resetColors();
    }

    /**
     * @param string $message
     * @param mixed  $args,...
     *
     * @return self
     */
    public function warning(string $message, ...$args) : self
    {
        return $this
            ->resetColors()
            ->color('black')
            ->background('yellow')
            ->line($message, ...$args)
            ->resetColors();
    }

    /**
     * @param string $message
     * @param mixed  $args,...
     *
     * @return self
     */
    public function info(string $message, ...$args) : self
    {
        return $this
            ->resetColors()
            ->color('bright-green')
            ->line($message, ...$args)
            ->resetColors();
    }

    /**
     * @return self
     */
    public function resetColors() : self
    {
        unset($this->color);
        unset($this->background);

        return $this;
    }

    /**
     * @return self
     */
    public function disableColors() : self
    {
        $this->colorDisabled = true;

        return $this;
    }

    /**
     * @return self
     */
    public function enableColors() : self
    {
        $this->colorDisabled = false;

        return $this;
    }

    /**
     * @param string $message
     *
     * @return self
     */
    protected function colorize(string $message) : string
    {
        if ($this->colorDisabled) {
            return $message;
        }

        $output = '';

        if (isset($this->color)) {
            $output .= "\033[{$this->color}m";
        }

        if (isset($this->background)) {
            $output .= "\033[{$this->background}m";
        }

        $output .= $message."\033[{$this->reset}m";

        return $output;
    }

    /**
     * @param string $string
     *
     * @return array
     */
    protected function parseColorString(string $string) : array
    {
        $parts = explode('-', $string, 2);

        list($color, $modifier) = count($parts) == 2
            ? [$parts[1], $parts[0]] : [$parts[0], 'regular'];

        if (
            !isset($this->colors[$color]) ||
            !isset($this->modifiers[$modifier])
        ) {
            throw new InvalidArgumentException("Invalid color [$string]");
        }

        return [$color, $modifier];
    }
}

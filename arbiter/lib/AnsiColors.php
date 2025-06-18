<?php

class AnsiColors {
  // Pentru tema întunecată.
  const DEFAULT = "\e[39m";
  const ERROR = "\e[91m";
  const WARNING = "\e[93m";
  const SUCCESS = "\e[92m";
  const INFO = "\e[39m";
  const DEBUG = "\e[37m";
  const CHIPS = [
    "\e[91m", "\e[92m", "\e[94m", "\e[97m", "\e[90m", "\e[93m",
  ];

  // Pentru tema luminoasă.
  // const DEFAULT = "\e[39m";
  // const ERROR = "\e[31m";
  // const WARNING = "\e[33m";
  // const SUCCESS = "\e[32m";
  // const INFO = "\e[39m";
  // const DEBUG = "\e[37m";
  // const CHIPS = [
  //   "\e[31m", "\e[32m", "\e[34m", "\e[37m", "\e[30m", "\e[33m",
  // ];
}

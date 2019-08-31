workflow "Main" {
  on = "push"
  resolves = ["composer install", "PHPQA"]
}

action "PHPQA" {
  uses = "docker://mickaelandrieu/phpqa-ga"
  secrets = ["GITHUB_TOKEN"]
  args = "--report --output=cli"
}

action "composer install" {
  uses = "MilesChou/composer-action@master"
  args = "install -q --no-ansi --no-interaction --no-scripts --no-suggest --no-progress --prefer-dist"
}

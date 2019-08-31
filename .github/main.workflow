workflow "Main" {
  on = "push"
  resolves = ["PHPQA"]
}

action "PHPQA" {
  uses = "docker://mickaelandrieu/phpqa-ga"
  secrets = ["GITHUB_TOKEN"]
  args = "--report --output=cli"
}

{
  description = "Devshell du projet UmanIT Dev Bundle";

  inputs = {
    php-dev-shell.url = "github:DjLeChuck/php-dev-shell";
    nixpkgs.follows = "php-dev-shell/nixpkgs";
  };

  outputs =
    { self, nixpkgs, ... }:
    let
      systems = [
        "x86_64-linux"
        "aarch64-linux"
        "x86_64-darwin"
        "aarch64-darwin"
      ];
      forEachSystem = f: nixpkgs.lib.genAttrs systems f;
    in
    {
      devShells = forEachSystem (
        system:
        let
          pkgs = import nixpkgs { inherit system; };

          php = pkgs.php84.buildEnv {
            extraConfig = ''
              date.timezone = Europe/Paris
              memory_limit = 512M
            '';
          };

          composer = pkgs.php84Packages.composer;
        in
        {
          default = pkgs.mkShell {
            packages = [
              php
              composer
            ];

            shellHook = ''
              echo "PHP:      ${php.version}"
              echo "Composer: ${composer.version}"
            '';
          };
        }
      );
    };
}

module.exports = function(grunt) {

    var skeletons           = 'skeletons/',
        pattern             = /\*slug\*/g,
        moduleDirectories   = '../../',
        filesToCreate       = ['permissions', 'user_account_settings'];

    var ifModuleExist = function(data) {
        return (!data.existing);
    }

    grunt.initConfig({
            pkg: grunt.file.readJSON('package.json'),
            build_info: grunt.file.read(skeletons + 'build_info.txt'),
            build_module: grunt.file.read(skeletons + 'build_module.txt'),
            build_info_content: function(slug) {
                return grunt.config.get('build_info').replace(pattern, slug.toUpperCase());
            },
            build_content_module: function(slug) {
                return grunt.config.get('build_module').replace(pattern, slug);
            }
        }
    );

    grunt.registerTask('default', ['prompt']);

    grunt.task.registerTask('prompt', 'questions to user', function() {

        var inquirer    = require('inquirer'),
            done        = this.async();

        inquirer.prompt([
            {
                name: 'existing',
                type: 'confirm',
                message: 'Do you have a deployment module ready to use ?',
                default: false
            },
            {
                name: 'slug',
                type: 'input',
                message: 'Project slug name:',
                validate: function(input) {
                    if(!(/^[a-z0-9\-\_]+$/).test(input)) {
                        return 'Alphanumeric, hyphen or underscore only';
                    }
                    return true;
                },
                when: ifModuleExist
            },
            {
                name: 'activate',
                type: 'confirm',
                message: 'Do you want activate your deployment module ?',
                default: false,
                when: ifModuleExist
            }
        ], function(input) {

            var slug = input.slug;
            var execSync = require('execSync');

            if(slug) {
                var moduleName = slug + '_deployment';
                grunt.file.write(moduleDirectories + moduleName + '/' + moduleName + '.info', grunt.config.get('build_info_content')(slug));
                grunt.file.write(moduleDirectories + moduleName + '/' + moduleName + '.module', grunt.config.get('build_content_module')(slug));

                var configFileContent = '<?php \n';
                for(var i=0; i< filesToCreate.length; i++) {
                    var configFile = filesToCreate[i];
                    grunt.file.write(moduleDirectories + moduleName + '/config/' + configFile + '.json');
                    configFileContent += 'define("' + configFile.toUpperCase() + '_FILE","' + configFile + '.json"); \n';
                }
                configFileContent += '?>';
                grunt.file.write('../app/config.inc', configFileContent);

                execSync.run('drush vset deployment_module_name ' + moduleName + ' -y');

                if(input.activate) {
                    execSync.run('drush en ' + moduleName + ' -y');
                }
            } else {
                grunt.log.writeln('Check the structure of your deployment package !!');
            }

            execSync.run('drush en d2f2 -y');

            done();
        });

    });

};
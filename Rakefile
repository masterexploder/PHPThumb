require 'rake'
require 'yaml'

module PhpThumbHelper
  ROOT_DIR      = File.expand_path(File.dirname(__FILE__))
  SRC_DIR       = File.join(ROOT_DIR, 'src')
  DOCS_DIR      = File.join(ROOT_DIR, 'apidocs')
  VERSION       = YAML.load(IO.read(File.join(ROOT_DIR, 'config.yml')))['CURRENT_VERSION'].gsub(' ', '-').downcase
  
  def self.bump_version (type = 'patch')
    data = YAML.load_file(File.join(ROOT_DIR, 'config.yml'))
    version = data['CURRENT_VERSION'].split('.')
    
    case type
    when "patch"
      version[2] = '0' + ((version[2].to_i)+1).to_s
    when "minor"
      version[1] = ((version[1].to_i)+1).to_s
    when "major"
      version[0] = ((version[0].to_i)+1).to_s
    end
    
    data['CURRENT_VERSION'] = version.join('.')
    
    File.open(File.join(ROOT_DIR, 'config.yml'), 'w') { |f| YAML.dump(data, f) }
  end
  
  def self.build_docs
    sh %{phpdoc -d #{PhpThumbHelper::SRC_DIR} -t #{PhpThumbHelper::DOCS_DIR} -o HTML:frames:DOM/earthli}
  end
end

desc "Creates API docs via phpdocumentor"
task :docs do
  PhpThumbHelper.build_docs
end

namespace :bump_version do
  desc "Bumps the patch version"
  task :patch do
    PhpThumbHelper.bump_version
  end
  desc "Bumps the minor version"
  task :minor do
    PhpThumbHelper.bump_version("patch")
  end
  desc "Bumps the major version"
  task :major do
    PhpThumbHelper.bump_version("major")
  end
end
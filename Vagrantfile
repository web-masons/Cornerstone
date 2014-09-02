# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

boxes = {
  'php' => {
    :box => 'ubuntu1204-chef1144',
    :boxurl => 'https://opscode-vm-bento.s3.amazonaws.com/vagrant/opscode_ubuntu-12.04_chef-11.4.4.box',
    :recipes => [
      'cornerstone::default'
    ],
    :json => {
      'cornerstone-vagrant' => {
        'project' => 'cornerstone',
        'siteslug' => 'vagrant.www.cornerstone'
      }
    }
  }
}

# define servers
servers = [
  {
    :hostname => 'vagrant.www.cornerstone.com',
    :ip => '192.168.2.2',
    :type => 'php',
    :primary => true
  }
]

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  #Setup hostmanager config to update the host files
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.ignore_private_ip = false
  config.hostmanager.include_offline = true
  config.vm.provision :hostmanager

  # Enable Synced Folders
  config.vm.synced_folder ".", "/vagrant"

  # Forward SSH Keys
  config.ssh.forward_agent = true

  # Berkshelf all the things
  [:up, :provision].each do |cmd|
    config.trigger.before cmd, :stdout => true do
      info 'Cleaning cookbook directory'
      run "rm -rf cookbooks"
      info 'Installing cookbook dependencies with berkshelf'
      run "berks vendor cookbooks"
    end
  end

  # Loop through all servers and configure them
  servers.each do |server|
    config.vm.define server[:hostname], primary: server[:primary] do |node_config|
      node_config.vm.box = boxes[server[:type]][:box]
      node_config.vm.box_url = boxes[server[:type]][:boxurl]
      node_config.vm.hostname = server[:hostname]
      node_config.vm.network :private_network, ip: server[:ip]

      node_config.hostmanager.aliases = server[:hostname]

      node_config.vm.provision :chef_solo do |chef|
        chef.cookbooks_path = [
          "cookbooks"
        ]

        chef.json = boxes[server[:type]][:json]

        boxes[server[:type]][:recipes].each do |recipe|
          chef.add_recipe recipe
        end
      end

    end
  end

end

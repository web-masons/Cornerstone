# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

boxes = {
  'php' => {
    :box => 'ubuntu1204-chef1144',
    :boxurl => 'https://opscode-vm-bento.s3.amazonaws.com/vagrant/opscode_ubuntu-12.04_chef-11.4.4.box',
    :recipes => [
      'web-developer-cookbook',
      'cornerstone-vagrant'
    ],
    :json => {
      'cornerstone-vagrant' => {
        'project' => 'cornerstone',
        'environment' => 'vagrant',
        'siteslug' => 'cornerstone'
      }
    }
  }
}

# define servers
servers = [
  { :hostname => 'cornerstone', :aliases => 'vagrant.cornerstone', :type => 'php', :primary => true }
]

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # Setup hostmanager config to update the host files
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.ignore_private_ip = false
  config.hostmanager.include_offline = true
  config.vm.provision :hostmanager

  # Forward our SSH Keys into the VM
  config.ssh.forward_agent = true

  # Enable Berkshelf plugin which will make cookbooks available to Vagrant
  config.berkshelf.enabled = true

  # Loop through all servers and configure them
  servers.each do |server|
    config.vm.define server[:hostname], primary: server[:primary] do |node_config|
      node_config.vm.box = boxes[server[:type]][:box]
      node_config.vm.box_url = boxes[server[:type]][:boxurl]
      node_config.vm.hostname = server[:hostname]
      node_config.vm.network :private_network, :auto_network => true
      node_config.hostmanager.aliases = server[:aliases]

      node_config.vm.provision :chef_solo do |chef|
        chef.json = boxes[server[:type]][:json]

        boxes[server[:type]][:recipes].each do |recipe|
          chef.add_recipe recipe
        end
      end
    end
  end

end

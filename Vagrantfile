# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

  # The namespace/name of the box we want to reference.  As found by searching the
  # Atlas directory (https://atlas.hashicorp.com/boxes/search)
  config.vm.box = "jakegroup/grid"
  # What ip address the machine should be available at.
  config.vm.network "private_network", ip: "192.168.32.14"

  # Optional: a hostname Vagrant should add to the hosts file for this machine
  config.vm.hostname = "grid.dev"

  # The shared folder (and permission options) for the guest machine.
  # Maps the director this file is in to /var/www on the guest machine.
  # So anything in this directory, will exist in /var/www (the web root)
  config.vm.synced_folder ".", "/var/www", :mount_options => ["dmode=777", "fmode=666"]
  
  # Name of the box that will show up in vagrant status calls.
  config.vm.define "Jake Grid"

  # Cleans up the name of the box when using the virtualbox GUI.
  # Just makes it easier to identify
  config.vm.provider "virtualbox" do |v|
    v.name = "jake-grid"
  end

  # Save databases from the VM to local folder when destroying VM
  config.trigger.before :destroy do
    info "Dumping databases to backup_dbs.sql..."
    run_remote  "bash /vagrant/dump_dbs.sh"
  end

  # Ask if you'd like to load dbs from local file when provisioning
  # a VM for the first time.
  config.vm.provision "trigger", :option => "value" do |trigger|
    trigger.fire do
      confirm = nil
      until ["Y", "y", "N", "n"].include?(confirm)
        confirm = ask "Load VM databases from backup_dbs.sql? (Y/N) "
      end
      exit unless confirm.upcase == "Y"
      info "Loading vagrant databases from backup_dbs.sql..."
      run_remote  "bash /vagrant/load_dbs.sh"
    end
  end 

end

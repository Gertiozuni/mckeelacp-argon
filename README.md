# IT Control Panel
Made with Laravel and Vue. 
A control panel that automated a bunch of tasks such as setting up Apple Classroom, Updating/Adding/Deleting iPads from the Cisco MDM, and a complete automated documentation of the network infrastructure. 

## Features
* [Update Apple Classroom](https://github.com/JeffreyHosler/mckeelacp#update-apple-classroom)
* [Cisco MDM](https://github.com/JeffreyHosler/mckeelacp#cisco-mdm)
* [Automated Network Documenation](https://github.com/JeffreyHosler/mckeelacp#automated-network-documenation)

### Update Apple Classroom

1. Takes 6 excel files generated from the country school board, and converts them into 9 csv files in a format Apple Classroom can read. 
   1. Files created are students, teachers, classes, rosters, courses and campuses.
1. Returns a zip file of the csv's and a complete list of changes.
   1. Changes include Students Added, Students Removed, Teachers Added, Teachers Removed

### Cisco MDM

1. Bulk Wipe
   1. Enter a list of serial numbers and it will search all Cisco Networks to wipe them. Returns a list of serial numbers it couldn't find.
1. Searching
   1. Find an iPad in any network based on its owner, mac address, or serial number

### Automated Network Documenation

1. Enter in the IP address of the switch to add it to the list.
   1. Script runs once a day and emails the technicans a detailed list of changes. Changes include:
      1. Switch Uptime
      1. VLAN change
      1. Port mode ( trunking, access )
1. A list of all ports, showing which are active and which are inactive. Along with some details about the port. 
1. A complete history of all changes made to the switch, and the port. 
  1. History includes who made the change, and what the change was. 
1. Updatating the switch sends telnet commands directly to the switch so that the switch can update without having to log into it.
  1. A technican with admin access can change the ports VLAN, mode, description, etc straight from the page


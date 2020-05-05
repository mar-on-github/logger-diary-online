use std::io;
use std::fs::File;
use std::io::prelude::*;
fn main() -> std::io::Result<()> {
	println!("Welcome! Please enter your username:");
	let mut username = String::new();
    io::stdin()
        .read_line(&mut username)
        .expect("Failed to read line");
	let userlogfile = username ".my.log";
	println!("Okay, your username is {} and your log is called {}.",username, userlogfile);
    let mut file = File::create(userlogfile)?;
	file.write_all(b"Hello, world!")?;
    Ok(())
}

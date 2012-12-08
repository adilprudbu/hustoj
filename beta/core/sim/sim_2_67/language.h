/*	This file is part of the software similarity tester SIM.
	Written by Dick Grune, Vrije Universiteit, Amsterdam.
	$Id: language.h,v 1.7 2012-06-09 08:09:18 Gebruiker Exp $
*/

/*
	The *lang.l files provide two interfaces:
	    language.[ch]	static data about the language
	    lang.[ch]		dynamic data about the input file's content
	This is language.[ch].
*/

/*
	The abstract class 'language' defines the routines Init_Language(),
	May_Be_Start_Of_Run() and Best_Run_Size(), which describe some
	properties of the language.
	These routines are provided by the *lang.l files.

	There is a dummy implementation language.c.

*/

extern void Init_Language(void);
extern int May_Be_Start_Of_Run(Token ch);
extern unsigned int Best_Run_Size(const Token *str, unsigned int size);
